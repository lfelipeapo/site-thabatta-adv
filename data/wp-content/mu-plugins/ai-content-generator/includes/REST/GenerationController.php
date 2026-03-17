<?php
/**
 * Controller de geração de conteúdo
 *
 * @package AICG\REST
 * @since   1.0.0
 */

namespace AICG\REST;

use AICG\API\GroqClient;
use AICG\Content\PostCreator;
use AICG\Content\Scheduler;
use AICG\Security\RateLimiter;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class GenerationController
 *
 * @package AICG\REST
 * @since   1.0.0
 */
class GenerationController
{
    /**
     * Handle da requisição de geração
     *
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function handle_request(\WP_REST_Request $request)
    {
        // Verifica rate limit
        $rate_limiter = new RateLimiter();
        $rate_check = $rate_limiter->check($request);

        if (is_wp_error($rate_check)) {
            return $rate_check;
        }

        // Obtém parâmetros
        $prompt = $request->get_param('prompt');
        $content_type = $request->get_param('content_type');
        $schedule_date = $request->get_param('schedule_date');
        $options = self::normalize_options((array) ($request->get_param('options') ?? []));
        $schedule_date = is_string($schedule_date) && $schedule_date !== '' ? $schedule_date : null;

        // Verifica se deve usar geração assíncrona
        $async = get_option('aicg_async_generation', true);

        if ($async) {
            return self::handle_async_generation($prompt, $content_type, $schedule_date, $options);
        }

        return self::handle_sync_generation($prompt, $content_type, $schedule_date, $options);
    }

    /**
     * Normaliza opções recebidas do frontend para o formato esperado no backend.
     *
     * @param array $options Opções enviadas pela interface
     * @return array
     */
    private static function normalize_options(array $options): array
    {
        if (empty($options['length']) && !empty($options['target_length'])) {
            $options['length'] = match (true) {
                $options['target_length'] <= 600 => 'short',
                $options['target_length'] >= 1500 => 'long',
                default => 'medium',
            };
        }

        if (isset($options['include_images'])) {
            $options['include_images'] = (bool) $options['include_images'];
        }

        if (!empty($options['category']) && is_array($options['category'])) {
            $options['category'] = array_values(array_filter(array_map('intval', $options['category'])));
        }

        return $options;
    }

    /**
     * Handle de geração síncrona
     *
     * @param string $prompt Prompt
     * @param string $content_type Tipo de conteúdo
     * @param string|null $schedule_date Data de agendamento
     * @param array $options Opções
     * @return \WP_REST_Response|\WP_Error
     */
    private static function handle_sync_generation(string $prompt, string $content_type, ?string $schedule_date, array $options)
    {
        $client = new GroqClient();

        // Verifica cache
        $prompt_hash = hash('sha256', strtolower(trim($prompt)));
        $cached = get_transient('aicg_cache_' . $prompt_hash);

        if ($cached !== false && get_option('aicg_cache_enabled', true)) {
            $result = json_decode($cached, true);
        } else {
            // Gera conteúdo
            $result = $client->generate_content($prompt, array_merge($options, [
                'content_type' => $content_type,
            ]));

            if (is_wp_error($result)) {
                return $result;
            }

            // Salva em cache
            if (get_option('aicg_cache_enabled', true)) {
                $ttl = (int) get_option('aicg_cache_ttl', DAY_IN_SECONDS);
                set_transient('aicg_cache_' . $prompt_hash, wp_json_encode($result), $ttl);
            }
        }

        // Cria o post
        $creator = new PostCreator();
        $post_data = $creator->create($result, array_merge($options, [
            'content_type' => $content_type,
            'schedule_date' => $schedule_date,
            'original_prompt' => $prompt,
        ]));

        if (is_wp_error($post_data)) {
            return $post_data;
        }

        // Incrementa rate limit
        $rate_limiter = new RateLimiter();
        $rate_limiter->increment();

        // Adiciona headers de rate limit
        $limit_info = $rate_limiter->get_limit_info();
        $headers = [
            'X-RateLimit-Limit' => $limit_info['limit'],
            'X-RateLimit-Remaining' => $limit_info['remaining'],
            'X-RateLimit-Reset' => $limit_info['reset'],
        ];

        return new \WP_REST_Response([
            'success' => true,
            'data' => [
                'job_id' => null, // Síncrono não tem job
                'post_id' => $post_data['post_id'],
                'post_type' => $post_data['post_type'],
                'status' => $post_data['status'],
                'scheduled_date' => $schedule_date,
                'edit_link' => $post_data['edit_link'],
                'preview_link' => $post_data['preview_link'],
                'generation_metadata' => [
                    'model_used' => $result['metadata']['model'] ?? 'unknown',
                    'tokens_input' => $result['metadata']['tokens_input'] ?? 0,
                    'tokens_output' => $result['metadata']['tokens_output'] ?? 0,
                    'tokens_total' => $result['metadata']['tokens_total'] ?? 0,
                ],
            ],
        ], 201, $headers);
    }

    /**
     * Handle de geração assíncrona
     *
     * @param string $prompt Prompt
     * @param string $content_type Tipo de conteúdo
     * @param string|null $schedule_date Data de agendamento
     * @param array $options Opções
     * @return \WP_REST_Response|\WP_Error
     */
    private static function handle_async_generation(string $prompt, string $content_type, ?string $schedule_date, array $options)
    {
        $scheduler = new Scheduler();

        // Cria job
        $job_id = $scheduler->create_job($prompt, [
            'content_type' => $content_type,
            'schedule_date' => $schedule_date,
            'options' => $options,
        ]);

        if (is_wp_error($job_id)) {
            return $job_id;
        }

        // Incrementa rate limit
        $rate_limiter = new RateLimiter();
        $rate_limiter->increment();

        // Adiciona headers de rate limit
        $limit_info = $rate_limiter->get_limit_info();
        $headers = [
            'X-RateLimit-Limit' => $limit_info['limit'],
            'X-RateLimit-Remaining' => $limit_info['remaining'],
            'X-RateLimit-Reset' => $limit_info['reset'],
        ];

        return new \WP_REST_Response([
            'success' => true,
            'data' => [
                'job_id' => $job_id,
                'status' => 'queued',
                'message' => esc_html__('Geração iniciada. Acompanhe o status via polling.', 'ai-content-generator'),
            ],
        ], 202, $headers);
    }
}
