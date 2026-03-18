<?php
/**
 * Registro de rotas REST API
 *
 * @package AICG\REST
 * @since   1.0.0
 */

namespace AICG\REST;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Routes
 *
 * Registra todos os endpoints da REST API
 *
 * @package AICG\REST
 * @since   1.0.0
 */
class Routes
{
    /**
     * Namespace da API
     *
     * @var string
     */
    private string $namespace = 'aicg/v1';

    /**
     * Construtor
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registra todas as rotas
     *
     * @return void
     */
    public function register_routes(): void
    {
        // Rota principal de geração
        register_rest_route($this->namespace, '/generate', [
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => [GenerationController::class, 'handle_request'],
                'permission_callback' => [$this, 'check_permission'],
                'args' => $this->get_generate_args(),
            ],
        ]);

        // Rota de status do job
        register_rest_route($this->namespace, '/status/(?P<job_id>[a-zA-Z0-9]+)', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [StatusController::class, 'get_status'],
                'permission_callback' => [$this, 'check_permission'],
                'args' => [
                    'job_id' => [
                        'type' => 'string',
                        'required' => true,
                        'validate_callback' => function ($param) {
                            return !empty($param) && is_string($param);
                        },
                    ],
                ],
            ],
        ]);

        // Rota de histórico
        register_rest_route($this->namespace, '/history', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [HistoryController::class, 'get_history'],
                'permission_callback' => [$this, 'check_permission'],
                'args' => [
                    'page' => [
                        'type' => 'integer',
                        'default' => 1,
                        'sanitize_callback' => 'absint',
                    ],
                    'per_page' => [
                        'type' => 'integer',
                        'default' => 20,
                        'sanitize_callback' => function ($param) {
                            return min(absint($param), 100);
                        },
                    ],
                    'content_type' => [
                        'type' => 'string',
                        'validate_callback' => function ($param) {
                            return in_array($param, ['post', 'page', 'all'], true);
                        },
                    ],
                ],
            ],
        ]);

        // Rota de deleção de conteúdo
        register_rest_route($this->namespace, '/content/(?P<post_id>\d+)', [
            [
                'methods' => \WP_REST_Server::DELETABLE,
                'callback' => [ContentController::class, 'delete_content'],
                'permission_callback' => function () {
                    return current_user_can('delete_posts');
                },
                'args' => [
                    'post_id' => [
                        'type' => 'integer',
                        'required' => true,
                        'validate_callback' => function ($param) {
                            return is_numeric($param) && $param > 0;
                        },
                    ],
                    'force' => [
                        'type' => 'boolean',
                        'default' => false,
                    ],
                ],
            ],
        ]);

        // Rota de configurações
        register_rest_route($this->namespace, '/settings', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [SettingsController::class, 'get_settings'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ],
            [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [SettingsController::class, 'update_settings'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args' => $this->get_settings_args(),
            ],
        ]);

        // Rota de validação de API key
        register_rest_route($this->namespace, '/validate-api', [
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => [SettingsController::class, 'validate_api_key'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
                'args' => [
                    'api_key' => [
                        'type' => 'string',
                        'required' => true,
                    ],
                ],
            ],
        ]);

        // Rota de modelos disponíveis
        register_rest_route($this->namespace, '/models', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [SettingsController::class, 'get_models'],
                'permission_callback' => [$this, 'check_permission'],
                'args' => [
                    'refresh' => [
                        'type' => 'boolean',
                        'default' => false,
                    ],
                ],
            ],
        ]);

        // Rota de estatísticas
        register_rest_route($this->namespace, '/stats', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [StatsController::class, 'get_stats'],
                'permission_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ],
        ]);

        // Rota para cancelar job
        register_rest_route($this->namespace, '/cancel/(?P<job_id>[a-zA-Z0-9]+)', [
            [
                'methods' => \WP_REST_Server::CREATABLE,
                'callback' => [StatusController::class, 'cancel_job'],
                'permission_callback' => [$this, 'check_permission'],
                'args' => [
                    'job_id' => [
                        'type' => 'string',
                        'required' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Verifica permissão para acesso geral
     *
     * @return bool|\WP_Error
     */
    public function check_permission()
    {
        if (!is_user_logged_in()) {
            return new \WP_Error(
                'rest_not_logged_in',
                esc_html__('Autenticação necessária.', 'ai-content-generator'),
                ['status' => 401]
            );
        }

        if (!current_user_can('edit_posts')) {
            return new \WP_Error(
                'rest_forbidden',
                esc_html__('Permissão insuficiente.', 'ai-content-generator'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Obtém argumentos para geração
     *
     * @return array
     */
    private function get_generate_args(): array
    {
        return [
            'prompt' => [
                'type' => 'string',
                'required' => true,
                'sanitize_callback' => 'sanitize_textarea_field',
                'validate_callback' => function ($param) {
                    $length = strlen($param);
                    if ($length < 10) {
                        return new \WP_Error(
                            'prompt_too_short',
                            esc_html__('O prompt deve ter pelo menos 10 caracteres.', 'ai-content-generator')
                        );
                    }
                    if ($length > 10000) {
                        return new \WP_Error(
                            'prompt_too_long',
                            esc_html__('O prompt deve ter no máximo 10000 caracteres.', 'ai-content-generator')
                        );
                    }
                    return true;
                },
            ],
            'content_type' => [
                'type' => 'string',
                'required' => true,
                'enum' => ['post', 'page'],
            ],
            'schedule_date' => [
                'sanitize_callback' => function ($param) {
                    if (empty($param)) {
                        return null;
                    }

                    return sanitize_text_field((string) $param);
                },
                'validate_callback' => function ($param) {
                    if (empty($param)) {
                        return true;
                    }
                    
                    $timestamp = strtotime($param);
                    if ($timestamp === false) {
                        return new \WP_Error(
                            'invalid_date',
                            esc_html__('Data de agendamento inválida.', 'ai-content-generator')
                        );
                    }
                    
                    // Verifica se data é futura
                    if ($timestamp <= current_time('timestamp')) {
                        return new \WP_Error(
                            'past_date',
                            esc_html__('A data de agendamento deve ser futura.', 'ai-content-generator')
                        );
                    }
                    
                    return true;
                },
            ],
            'options' => [
                'type' => 'object',
                'default' => [],
                'properties' => [
                    'tone' => [
                        'type' => 'string',
                        'enum' => ['professional', 'casual', 'technical', 'persuasive', 'narrative'],
                    ],
                    'length' => [
                        'type' => 'string',
                        'enum' => ['short', 'medium', 'long'],
                    ],
                    'target_length' => [
                        'type' => 'integer',
                        'minimum' => 300,
                        'maximum' => 5000,
                    ],
                    'include_images' => [
                        'type' => 'boolean',
                    ],
                    'seo_focus_keyword' => [
                        'type' => 'string',
                    ],
                    'category' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Obtém argumentos para configurações
     *
     * @return array
     */
    private function get_settings_args(): array
    {
        return [
            'api_key' => [
                'type' => 'string',
            ],
            'default_model' => [
                'type' => 'string',
            ],
            'default_tone' => [
                'type' => 'string',
                'enum' => ['professional', 'casual', 'technical', 'persuasive', 'narrative'],
            ],
            'default_length' => [
                'type' => 'string',
                'enum' => ['short', 'medium', 'long'],
            ],
            'include_images' => [
                'type' => 'boolean',
            ],
            'cache_enabled' => [
                'type' => 'boolean',
            ],
            'async_generation' => [
                'type' => 'boolean',
            ],
        ];
    }
}
