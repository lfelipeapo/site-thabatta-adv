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
        $settings = [
            'api_key_configured' => !empty(get_option('aicg_api_key_encrypted')),
            'default_model' => get_option('aicg_default_model', 'llama-3.3-70b-versatile'),
            'default_tone' => get_option('aicg_default_tone', 'professional'),
            'default_length' => get_option('aicg_default_length', 'medium'),
            'include_images' => (bool) get_option('aicg_include_images', true),
            'cache_enabled' => (bool) get_option('aicg_cache_enabled', true),
            'async_generation' => (bool) get_option('aicg_async_generation', true),
            'available_models' => get_option('aicg_available_models', []),
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
            'async_generation',
        ];

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                update_option('aicg_' . $field, $params[$field]);
            }
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
    public static function get_models()
    {
        $models = get_option('aicg_available_models', []);

        // Se não tiver modelos em cache, tenta buscar
        if (empty($models)) {
            $client = new GroqClient();
            $result = $client->get_available_models();

            if (!is_wp_error($result)) {
                $models = [];
                foreach ($result as $model) {
                    $models[] = [
                        'id' => $model['id'],
                        'name' => $model['id'],
                    ];
                }
                update_option('aicg_available_models', $models);
            }
        }

        // Modelos padrão se nenhum encontrado
        if (empty($models)) {
            $models = [
                ['id' => 'llama-3.3-70b-versatile', 'name' => 'Llama 3.3 70B'],
                ['id' => 'mixtral-8x7b-32768', 'name' => 'Mixtral 8x7B'],
                ['id' => 'gemma-7b-it', 'name' => 'Gemma 7B'],
            ];
        }

        return new \WP_REST_Response([
            'success' => true,
            'data' => $models,
        ], 200);
    }
}
