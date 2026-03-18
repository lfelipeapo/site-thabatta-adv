<?php
/**
 * Controller de configurações
 *
 * @package AICG\REST
 * @since   1.0.0
 */

namespace AICG\REST;

use AICG\API\GroqClient;
use AICG\Security\Encryption;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class SettingsController
 *
 * @package AICG\REST
 * @since   1.0.0
 */
class SettingsController
{
    /**
     * Obtém configurações
     *
     * @return \WP_REST_Response
     */
    public static function get_settings(): \WP_REST_Response
    {
        $available_models = get_option('aicg_available_models', []);
        $async_available = !(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON);

        if (empty($available_models) && !empty(get_option('aicg_api_key_encrypted'))) {
            $client = new GroqClient();
            $models = $client->get_available_models();

            if (!is_wp_error($models)) {
                $available_models = $models;
            }
        }

        $default_model = get_option('aicg_default_model', '');

        if (!empty($available_models) && !in_array($default_model, array_column($available_models, 'id'), true)) {
            $default_model = $available_models[0]['id'];
            update_option('aicg_default_model', $default_model);
        }

        $settings = [
            'api_key_configured' => !empty(get_option('aicg_api_key_encrypted')),
            'default_model' => $default_model,
            'default_tone' => get_option('aicg_default_tone', 'professional'),
            'default_length' => get_option('aicg_default_length', 'medium'),
            'include_images' => (bool) get_option('aicg_include_images', true),
            'cache_enabled' => (bool) get_option('aicg_cache_enabled', true),
            'async_generation' => (bool) get_option('aicg_async_generation', true),
            'async_available' => $async_available,
            'available_models' => $available_models,
        ];

        return new \WP_REST_Response([
            'success' => true,
            'data' => $settings,
        ], 200);
    }

    /**
     * Atualiza configurações
     *
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function update_settings(\WP_REST_Request $request)
    {
        $params = $request->get_params();

        // Atualiza API key se fornecida
        if (isset($params['api_key']) && !empty($params['api_key'])) {
            $encryption = new Encryption();
            $encrypted = $encryption->encrypt($params['api_key']);
            update_option('aicg_api_key_encrypted', $encrypted);
        }

        // Atualiza outros campos
        $fields = [
            'default_model',
            'default_tone',
            'default_length',
            'include_images',
            'cache_enabled',
        ];

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                update_option('aicg_' . $field, $params[$field]);
            }
        }

        if (isset($params['async_generation'])) {
            $async_available = !(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON);
            update_option('aicg_async_generation', $async_available ? (bool) $params['async_generation'] : false);
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => esc_html__('Configurações atualizadas.', 'ai-content-generator'),
        ], 200);
    }

    /**
     * Valida API key
     *
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function validate_api_key(\WP_REST_Request $request)
    {
        $api_key = $request->get_param('api_key');

        if (empty($api_key)) {
            return new \WP_Error(
                'empty_api_key',
                esc_html__('Chave API não fornecida.', 'ai-content-generator')
            );
        }

        // Temporariamente salva a chave para teste
        $old_key = get_option('aicg_api_key_encrypted');
        
        $encryption = new Encryption();
        update_option('aicg_api_key_encrypted', $encryption->encrypt($api_key));

        // Testa conexão
        $client = new GroqClient();
        $result = $client->validate_connection();

        // Restaura chave antiga se falhou
        if (is_wp_error($result)) {
            update_option('aicg_api_key_encrypted', $old_key);
            return $result;
        }

        $client->get_available_models(true);

        // Mantém nova chave
        return new \WP_REST_Response([
            'success' => true,
            'message' => esc_html__('Chave API válida!', 'ai-content-generator'),
        ], 200);
    }

    /**
     * Obtém modelos disponíveis
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public static function get_models(\WP_REST_Request $request)
    {
        $refresh = rest_sanitize_boolean($request->get_param('refresh'));
        $models = get_option('aicg_available_models', []);

        if ($refresh || empty($models)) {
            $client = new GroqClient();
            $result = $client->get_available_models($refresh);

            if (!is_wp_error($result)) {
                $models = $result;
            }
        }

        if (empty($models)) {
            $default_model = get_option('aicg_default_model', '');
            if (!empty($default_model)) {
                $models = [
                    [
                        'id' => $default_model,
                        'name' => $default_model,
                    ],
                ];
            }
        }

        return new \WP_REST_Response([
            'success' => true,
            'data' => $models,
        ], 200);
    }
}
