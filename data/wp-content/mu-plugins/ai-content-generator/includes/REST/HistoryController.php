<?php
/**
 * Controller de histórico
 *
 * @package AICG\REST
 * @since   1.0.0
 */

namespace AICG\REST;

use AICG\Content\Scheduler;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class HistoryController
 *
 * @package AICG\REST
 * @since   1.0.0
 */
class HistoryController
{
    /**
     * Obtém histórico de gerações
     *
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response
     */
    public static function get_history(\WP_REST_Request $request): \WP_REST_Response
    {
        $page = $request->get_param('page');
        $per_page = $request->get_param('per_page');
        $content_type = $request->get_param('content_type');

        global $wpdb;

        $table = $wpdb->prefix . 'aicg_jobs';
        $where = ['1=1'];
        $args = [];

        // Filtra por usuário (exceto admins)
        if (!current_user_can('edit_others_posts')) {
            $where[] = 'user_id = %d';
            $args[] = get_current_user_id();
        }

        // Filtra por tipo de conteúdo
        if (!empty($content_type) && $content_type !== 'all') {
            $where[] = 'content_type = %s';
            $args[] = $content_type;
        }

        $where_clause = implode(' AND ', $where);

        // Conta total
        $total = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE {$where_clause}", $args)
        );

        // Obtém resultados paginados
        $offset = ($page - 1) * $per_page;
        $args[] = $per_page;
        $args[] = $offset;

        $jobs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} 
                 WHERE {$where_clause}
                 ORDER BY created_at DESC 
                 LIMIT %d OFFSET %d",
                $args
            ),
            ARRAY_A
        );

        // Processa resultados
        $items = [];
        foreach ($jobs as $job) {
            $metadata = json_decode($job['metadata'], true);
            
            $items[] = [
                'job_id' => $job['job_id'],
                'status' => $job['status'],
                'content_type' => $job['content_type'],
                'created_at' => $job['created_at'],
                'completed_at' => $job['completed_at'],
                'post_id' => $job['post_id'] ? (int) $job['post_id'] : null,
                'prompt_preview' => !empty($metadata['prompt']) 
                    ? substr($metadata['prompt'], 0, 100) . '...' 
                    : '',
            ];
        }

        $total_pages = ceil($total / $per_page);

        $response = new \WP_REST_Response([
            'success' => true,
            'data' => [
                'items' => $items,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $per_page,
                    'total' => (int) $total,
                    'total_pages' => $total_pages,
                ],
            ],
        ], 200);

        // Headers de paginação
        $response->header('X-WP-Total', $total);
        $response->header('X-WP-TotalPages', $total_pages);

        return $response;
    }
}
