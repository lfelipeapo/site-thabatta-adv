<?php
/**
 * Página de histórico
 *
 * @package AICG
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$table = $wpdb->prefix . 'aicg_jobs';
$user_id = get_current_user_id();
$can_view_all = current_user_can('edit_others_posts');

// Filtros
$status_filter = isset($_GET['status']) ? sanitize_key($_GET['status']) : '';
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Query
$where = ['1=1'];
$args = [];

if (!$can_view_all) {
    $where[] = 'user_id = %d';
    $args[] = $user_id;
}

if ($status_filter) {
    $where[] = 'status = %s';
    $args[] = $status_filter;
}

$where_clause = implode(' AND ', $where);

// Total
$total = $wpdb->get_var(
    $wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE {$where_clause}", $args)
);

// Jobs
$args[] = $per_page;
$args[] = $offset;

$jobs = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT j.*, u.display_name as user_name 
         FROM {$table} j 
         LEFT JOIN {$wpdb->users} u ON j.user_id = u.ID 
         WHERE {$where_clause}
         ORDER BY j.created_at DESC 
         LIMIT %d OFFSET %d",
        $args
    )
);

$total_pages = ceil($total / $per_page);

// Status labels
$status_labels = [
    'pending' => esc_html__('Pendente', 'ai-content-generator'),
    'processing' => esc_html__('Processando', 'ai-content-generator'),
    'completed' => esc_html__('Concluído', 'ai-content-generator'),
    'failed' => esc_html__('Falhou', 'ai-content-generator'),
    'cancelled' => esc_html__('Cancelado', 'ai-content-generator'),
];

$status_classes = [
    'pending' => 'status-pending',
    'processing' => 'status-processing',
    'completed' => 'status-completed',
    'failed' => 'status-failed',
    'cancelled' => 'status-cancelled',
];

?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <!-- Filtros -->
    <form method="get" action="" class="aicg-filters">
        <input type="hidden" name="page" value="ai-content-generator-history">
        
        <select name="status">
            <option value="">
                <?php esc_html_e('Todos os Status', 'ai-content-generator'); ?>
            </option>
            <?php foreach ($status_labels as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($status_filter, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php submit_button(esc_html__('Filtrar', 'ai-content-generator'), 'secondary', '', false); ?>
    </form>

    <!-- Tabela -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'ai-content-generator'); ?></th>
                <?php if ($can_view_all): ?>
                    <th><?php esc_html_e('Usuário', 'ai-content-generator'); ?></th>
                <?php endif; ?>
                <th><?php esc_html_e('Tipo', 'ai-content-generator'); ?></th>
                <th><?php esc_html_e('Status', 'ai-content-generator'); ?></th>
                <th><?php esc_html_e('Prompt', 'ai-content-generator'); ?></th>
                <th><?php esc_html_e('Criado', 'ai-content-generator'); ?></th>
                <th><?php esc_html_e('Ações', 'ai-content-generator'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jobs)): ?>
                <tr>
                    <td colspan="<?php echo $can_view_all ? 7 : 6; ?>" class="no-items">
                        <?php esc_html_e('Nenhum registro encontrado.', 'ai-content-generator'); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($jobs as $job): 
                    $metadata = json_decode($job->metadata, true);
                    $prompt = $metadata['prompt'] ?? '';
                    $prompt_preview = strlen($prompt) > 60 ? substr($prompt, 0, 60) . '...' : $prompt;
                ?>
                    <tr>
                        <td><code><?php echo esc_html(substr($job->job_id, 0, 8)); ?></code></td>
                        <?php if ($can_view_all): ?>
                            <td><?php echo esc_html($job->user_name); ?></td>
                        <?php endif; ?>
                        <td><?php echo esc_html($job->content_type); ?></td>
                        <td>
                            <span class="aicg-status <?php echo esc_attr($status_classes[$job->status] ?? ''); ?>">
                                <?php echo esc_html($status_labels[$job->status] ?? $job->status); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($prompt_preview); ?></td>
                        <td>
                            <?php echo esc_html(human_time_diff(strtotime($job->created_at), current_time('timestamp'))); ?> 
                            <?php esc_html_e('atrás', 'ai-content-generator'); ?>
                        </td>
                        <td>
                            <?php if ($job->post_id): ?>
                                <a href="<?php echo esc_url(get_edit_post_link($job->post_id)); ?>" class="button button-small">
                                    <?php esc_html_e('Editar', 'ai-content-generator'); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($job->status === 'failed' && $job->error_message): ?>
                                <span class="dashicons dashicons-warning" title="<?php echo esc_attr($job->error_message); ?>"></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginação -->
    <?php if ($total_pages > 1): ?>
        <div class="tablenav">
            <div class="tablenav-pages">
                <span class="displaying-num">
                    <?php 
                    printf(
                        /* translators: %s: Number of items */
                        esc_html(_n('%s item', '%s itens', $total, 'ai-content-generator')),
                        number_format_i18n($total)
                    ); 
                    ?>
                </span>
                <span class="pagination-links">
                    <?php
                    echo paginate_links([
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'total' => $total_pages,
                        'current' => $page,
                    ]);
                    ?>
                </span>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.aicg-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
}
.status-pending {
    background: #f0f0f1;
    color: #3c434a;
}
.status-processing {
    background: #c5d9ed;
    color: #1d2327;
}
.status-completed {
    background: #c6e1c6;
    color: #5b841b;
}
.status-failed {
    background: #eba3a3;
    color: #761919;
}
.status-cancelled {
    background: #f0f0f1;
    color: #646970;
}
.aicg-filters {
    margin: 20px 0;
}
.aicg-filters select {
    margin-right: 10px;
}
</style>
