<?php
/**
 * Agendador de conteúdo
 *
 * @package AICG\Content
 * @since   1.0.0
 */

namespace AICG\Content;

use AICG\API\GroqClient;
use AICG\Core\Logger;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Scheduler
 *
 * Gerencia jobs assíncronos e agendamento de conteúdo
 *
 * @package AICG\Content
 * @since   1.0.0
 */
class Scheduler
{
    /**
     * Logger
     *
     * @var Logger
     */
    private Logger $logger;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->logger = new Logger();
        $this->setup_hooks();
    }

    /**
     * Configura hooks
     *
     * @return void
     */
    private function setup_hooks(): void
    {
        // Hooks de cron
        add_action('aicg_process_pending_jobs', [$this, 'process_pending_jobs']);
        add_action('aicg_process_job', [$this, 'process_single_job'], 10, 1);
        add_action('aicg_cleanup_logs', [$this, 'cleanup_old_logs']);
        add_action('aicg_sync_models', [$this, 'sync_available_models']);

        // Intervalo customizado de cron
        add_filter('cron_schedules', [$this, 'add_cron_intervals']);
    }

    /**
     * Adiciona intervalos customizados de cron
     *
     * @param array $schedules Agendamentos existentes
     * @return array
     */
    public function add_cron_intervals(array $schedules): array
    {
        $schedules['aicg_every_2_minutes'] = [
            'interval' => 120,
            'display' => esc_html__('A cada 2 minutos', 'ai-content-generator'),
        ];

        $schedules['aicg_every_5_minutes'] = [
            'interval' => 300,
            'display' => esc_html__('A cada 5 minutos', 'ai-content-generator'),
        ];

        return $schedules;
    }

    /**
     * Cria um novo job de geração
     *
     * @param string $prompt Prompt do usuário
     * @param array $options Opções de geração
     * @return string|\WP_Error ID do job ou erro
     */
    public function create_job(string $prompt, array $options = [])
    {
        global $wpdb;

        $job_id = $this->generate_job_id();
        $user_id = get_current_user_id();
        $prompt_hash = hash('sha256', strtolower(trim($prompt)));

        $result = $wpdb->insert(
            $wpdb->prefix . 'aicg_jobs',
            [
                'job_id' => $job_id,
                'user_id' => $user_id,
                'status' => 'pending',
                'prompt_hash' => $prompt_hash,
                'content_type' => $options['content_type'] ?? 'post',
                'metadata' => wp_json_encode([
                    'prompt' => $prompt,
                    'options' => $options,
                ]),
                'created_at' => current_time('mysql'),
            ],
            ['%s', '%d', '%s', '%s', '%s', '%s', '%s']
        );

        if ($result === false) {
            return new \WP_Error(
                'job_creation_failed',
                esc_html__('Falha ao criar job de geração.', 'ai-content-generator')
            );
        }

        // Agenda processamento imediato
        wp_schedule_single_event(time(), 'aicg_process_job', [$job_id]);

        $this->logger->info('Job criado', [
            'job_id' => $job_id,
            'user_id' => $user_id,
        ]);

        return $job_id;
    }

    /**
     * Processa jobs pendentes
     *
     * @return void
     */
    public function process_pending_jobs(): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aicg_jobs';

        // Busca jobs pendentes
        $jobs = $wpdb->get_results(
            "SELECT * FROM {$table} 
             WHERE status = 'pending' 
             AND (started_at IS NULL OR started_at < DATE_SUB(NOW(), INTERVAL 10 MINUTE))
             ORDER BY created_at ASC
             LIMIT 5"
        );

        foreach ($jobs as $job) {
            $this->process_single_job($job->job_id);
        }
    }

    /**
     * Processa um job individual
     *
     * @param string $job_id ID do job
     * @return void
     */
    public function process_single_job(string $job_id): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aicg_jobs';

        // Obtém job
        $job = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE job_id = %s", $job_id)
        );

        if (!$job || $job->status !== 'pending') {
            return;
        }

        // Marca como processando
        $wpdb->update(
            $table,
            ['status' => 'processing', 'started_at' => current_time('mysql')],
            ['job_id' => $job_id],
            ['%s', '%s'],
            ['%s']
        );

        // Decodifica metadados
        $metadata = json_decode($job->metadata, true);
        $prompt = $metadata['prompt'] ?? '';
        $options = $metadata['options'] ?? [];

        try {
            // Inicializa cliente Groq
            $client = new GroqClient();

            // Verifica cache
            $cached_result = $this->get_cached_result($job->prompt_hash);
            
            if ($cached_result) {
                $result = $cached_result;
                $this->logger->info('Cache hit for job', ['job_id' => $job_id]);
            } else {
                // Gera conteúdo
                $result = $client->generate_content($prompt, $options);

                if (is_wp_error($result)) {
                    throw new \Exception($result->get_error_message());
                }

                // Salva em cache
                $this->cache_result($job->prompt_hash, $result);
            }

            // Cria o post
            $creator = new PostCreator();
            $post_data = $creator->create($result, array_merge($options, [
                'original_prompt' => $prompt,
            ]));

            if (is_wp_error($post_data)) {
                throw new \Exception($post_data->get_error_message());
            }

            // Atualiza job como completo
            $wpdb->update(
                $table,
                [
                    'status' => 'completed',
                    'completed_at' => current_time('mysql'),
                    'post_id' => $post_data['post_id'],
                    'result_data' => wp_json_encode($post_data),
                ],
                ['job_id' => $job_id],
                ['%s', '%s', '%d', '%s'],
                ['%s']
            );

            $this->logger->info('Job completed', [
                'job_id' => $job_id,
                'post_id' => $post_data['post_id'],
            ]);

        } catch (\Exception $e) {
            // Marca como falho
            $wpdb->update(
                $table,
                [
                    'status' => 'failed',
                    'completed_at' => current_time('mysql'),
                    'error_message' => $e->getMessage(),
                ],
                ['job_id' => $job_id],
                ['%s', '%s', '%s'],
                ['%s']
            );

            $this->logger->error('Job failed', [
                'job_id' => $job_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtém status de um job
     *
     * @param string $job_id ID do job
     * @return array|\WP_Error
     */
    public function get_job_status(string $job_id)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aicg_jobs';

        $job = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE job_id = %s", $job_id)
        );

        if (!$job) {
            return new \WP_Error(
                'job_not_found',
                esc_html__('Job não encontrado.', 'ai-content-generator')
            );
        }

        $response = [
            'job_id' => $job->job_id,
            'status' => $job->status,
            'created_at' => $job->created_at,
            'started_at' => $job->started_at,
            'completed_at' => $job->completed_at,
        ];

        // Calcula progresso estimado
        if ($job->status === 'pending') {
            $response['progress_percent'] = 0;
        } elseif ($job->status === 'processing') {
            $response['progress_percent'] = 50;
        } elseif ($job->status === 'completed') {
            $response['progress_percent'] = 100;
            
            // Inclui dados do post criado
            if (!empty($job->result_data)) {
                $result_data = json_decode($job->result_data, true);
                $response['result'] = $result_data;
            }
        } elseif ($job->status === 'failed') {
            $response['error'] = $job->error_message;
        }

        return $response;
    }

    /**
     * Obtém resultado em cache
     *
     * @param string $prompt_hash Hash do prompt
     * @return array|false
     */
    private function get_cached_result(string $prompt_hash)
    {
        if (!get_option('aicg_cache_enabled', true)) {
            return false;
        }

        $cached = get_transient('aicg_cache_' . $prompt_hash);
        
        if ($cached !== false) {
            return json_decode($cached, true);
        }

        return false;
    }

    /**
     * Salva resultado em cache
     *
     * @param string $prompt_hash Hash do prompt
     * @param array $result Resultado a cachear
     * @return void
     */
    private function cache_result(string $prompt_hash, array $result): void
    {
        if (!get_option('aicg_cache_enabled', true)) {
            return;
        }

        $ttl = (int) get_option('aicg_cache_ttl', DAY_IN_SECONDS);
        set_transient('aicg_cache_' . $prompt_hash, wp_json_encode($result), $ttl);
    }

    /**
     * Limpa logs antigos
     *
     * @return void
     */
    public function cleanup_old_logs(): void
    {
        $logger = new Logger();
        $logger->cleanup_old_logs();
    }

    /**
     * Sincroniza modelos disponíveis
     *
     * @return void
     */
    public function sync_available_models(): void
    {
        $client = new GroqClient();
        $models = $client->get_available_models();

        if (!is_wp_error($models)) {
            $model_list = [];
            foreach ($models as $model) {
                $model_list[] = [
                    'id' => $model['id'],
                    'name' => $model['id'],
                    'active' => $model['active'] ?? true,
                ];
            }

            update_option('aicg_available_models', $model_list);
            
            $this->logger->info('Models synced', ['count' => count($model_list)]);
        }
    }

    /**
     * Gera ID único para job
     *
     * @return string
     */
    private function generate_job_id(): string
    {
        return md5(uniqid('aicg_', true) . wp_rand());
    }

    /**
     * Cancela um job pendente
     *
     * @param string $job_id ID do job
     * @return bool|\WP_Error
     */
    public function cancel_job(string $job_id)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aicg_jobs';

        $job = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE job_id = %s", $job_id)
        );

        if (!$job) {
            return new \WP_Error(
                'job_not_found',
                esc_html__('Job não encontrado.', 'ai-content-generator')
            );
        }

        // Só pode cancelar jobs pendentes
        if ($job->status !== 'pending') {
            return new \WP_Error(
                'job_not_cancellable',
                esc_html__('Apenas jobs pendentes podem ser cancelados.', 'ai-content-generator')
            );
        }

        $result = $wpdb->update(
            $table,
            ['status' => 'cancelled', 'completed_at' => current_time('mysql')],
            ['job_id' => $job_id],
            ['%s', '%s'],
            ['%s']
        );

        if ($result === false) {
            return new \WP_Error(
                'cancel_failed',
                esc_html__('Falha ao cancelar job.', 'ai-content-generator')
            );
        }

        return true;
    }

    /**
     * Obtém histórico de jobs de um usuário
     *
     * @param int $user_id ID do usuário
     * @param int $limit Limite de resultados
     * @return array
     */
    public function get_user_jobs(int $user_id, int $limit = 20): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'aicg_jobs';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} 
                 WHERE user_id = %d 
                 ORDER BY created_at DESC 
                 LIMIT %d",
                $user_id,
                $limit
            ),
            ARRAY_A
        );
    }
}
