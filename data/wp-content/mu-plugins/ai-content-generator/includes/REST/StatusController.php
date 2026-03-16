<?php
/**
 * Controller de status de jobs
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
 * Class StatusController
 *
 * @package AICG\REST
 * @since   1.0.0
 */
class StatusController
{
    /**
     * Obtém status de um job
     *
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function get_status(\WP_REST_Request $request)
    {
        $job_id = $request->get_param('job_id');

        $scheduler = new Scheduler();
        $status = $scheduler->get_job_status($job_id);

        if (is_wp_error($status)) {
            return $status;
        }

        return new \WP_REST_Response([
            'success' => true,
            'data' => $status,
        ], 200);
    }

    /**
     * Cancela um job
     *
     * @param \WP_REST_Request $request Requisição
     * @return \WP_REST_Response|\WP_Error
     */
    public static function cancel_job(\WP_REST_Request $request)
    {
        $job_id = $request->get_param('job_id');

        $scheduler = new Scheduler();
        $result = $scheduler->cancel_job($job_id);

        if (is_wp_error($result)) {
            return $result;
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => esc_html__('Job cancelado com sucesso.', 'ai-content-generator'),
        ], 200);
    }
}
