<?php
/**
 * Controller de estatísticas
 *
 * @package AICG\REST
 * @since   1.0.0
 */

namespace AICG\REST;

use AICG\API\GroqClient;
use AICG\Content\PostCreator;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class StatsController
 *
 * @package AICG\REST
 * @since   1.0.0
 */
class StatsController
{
    /**
     * Obtém estatísticas
     *
     * @return \WP_REST_Response
     */
    public static function get_stats(): \WP_REST_Response
    {
        // Estatísticas de posts
        $post_stats = PostCreator::get_statistics();

        // Estatísticas de uso da API
        $client = new GroqClient();
        $usage_stats = $client->get_usage_stats();

        // Jobs recentes
        global $wpdb;
        $table = $wpdb->prefix . 'aicg_jobs';

        $recent_jobs = $wpdb->get_results(
            "SELECT status, COUNT(*) as count 
             FROM {$table} 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY status",
            OBJECT_K
        );

        $jobs_stats = [
            'total' => 0,
            'completed' => (int) ($recent_jobs['completed']->count ?? 0),
            'failed' => (int) ($recent_jobs['failed']->count ?? 0),
            'pending' => (int) ($recent_jobs['pending']->count ?? 0),
        ];
        $jobs_stats['total'] = $jobs_stats['completed'] + $jobs_stats['failed'] + $jobs_stats['pending'];

        // Taxa de sucesso
        $success_rate = $jobs_stats['total'] > 0 
            ? round(($jobs_stats['completed'] / $jobs_stats['total']) * 100, 1) 
            : 0;

        return new \WP_REST_Response([
            'success' => true,
            'data' => [
                'posts' => $post_stats,
                'api_usage' => $usage_stats,
                'jobs' => $jobs_stats,
                'success_rate' => $success_rate,
            ],
        ], 200);
    }
}
