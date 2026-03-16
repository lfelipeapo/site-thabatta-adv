<?php
/**
 * Controller de conteúdo
 *
 * @package AICG\REST
 * @since   1.0.0
 */

namespace AICG\REST;

use AICG\Content\PostCreator;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ContentController
 *
 * @package AICG\REST
 * @since   1.0.0
 */
class ContentController
{
    /**
     * Deleta conteúdo gerado
     *
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function delete_content(\WP_REST_Request $request)
    {
        $post_id = (int) $request->get_param('post_id');
        $force = $request->get_param('force') ?? false;

        $post = get_post($post_id);

        if (!$post) {
            return new \WP_Error(
                'post_not_found',
                esc_html__('Post não encontrado.', 'ai-content-generator')
            );
        }

        // Verifica se foi gerado por IA
        if (!PostCreator::is_ai_generated($post_id)) {
            return new \WP_Error(
                'not_ai_generated',
                esc_html__('Este post não foi gerado por IA.', 'ai-content-generator')
            );
        }

        // Verifica permissões
        if (!current_user_can('delete_post', $post_id)) {
            return new \WP_Error(
                'permission_denied',
                esc_html__('Sem permissão para excluir este post.', 'ai-content-generator')
            );
        }

        // Força deleção se solicitado (bypass da lixeira)
        if ($force && !current_user_can('delete_others_posts')) {
            return new \WP_Error(
                'force_delete_not_allowed',
                esc_html__('Você não tem permissão para excluir permanentemente.', 'ai-content-generator')
            );
        }

        $result = wp_delete_post($post_id, $force);

        if (!$result) {
            return new \WP_Error(
                'delete_failed',
                esc_html__('Falha ao excluir o post.', 'ai-content-generator')
            );
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => $force 
                ? esc_html__('Post excluído permanentemente.', 'ai-content-generator')
                : esc_html__('Post movido para a lixeira.', 'ai-content-generator'),
            'post_id' => $post_id,
        ], 200);
    }
}
