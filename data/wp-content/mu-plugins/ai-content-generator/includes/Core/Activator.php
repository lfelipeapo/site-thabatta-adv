<?php
/**
 * Classe de ativação do plugin
 *
 * @package AICG\Core
 * @since   1.0.0
 */

namespace AICG\Core;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Activator
 *
 * Gerencia a ativação do plugin e configuração inicial
 *
 * @package AICG\Core
 * @since   1.0.0
 */
class Activator
{
    /**
     * Executa a ativação do plugin
     *
     * @return void
     */
    public static function activate(): void
    {
        global $wpdb;

        // Verifica versão do WordPress
        if (version_compare(get_bloginfo('version'), AICG_MIN_WP_VERSION, '<')) {
            wp_die(
                sprintf(
                    /* translators: %s: Minimum WordPress version */
                    esc_html__('Este plugin requer WordPress %s ou superior.', 'ai-content-generator'),
                    AICG_MIN_WP_VERSION
                ),
                esc_html__('Erro de Ativação', 'ai-content-generator'),
                ['back_link' => true]
            );
        }

        // Cria tabelas customizadas
        self::create_tables();

        // Registra opções padrão
        self::register_default_options();

        // Cria diretório de uploads dedicado
        self::create_upload_directory();

        // Agenda eventos cron
        self::schedule_cron_events();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Marca que o onboarding precisa ser completado
        if (get_option('aicg_onboarding_completed') === false) {
            update_option('aicg_onboarding_redirect', true);
        }
    }

    /**
     * Cria tabelas customizadas no banco de dados
     *
     * @return void
     */
    private static function create_tables(): void
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_jobs = $wpdb->prefix . 'aicg_jobs';
        $table_logs = $wpdb->prefix . 'aicg_logs';

        // Tabela de jobs assíncronos
        $sql_jobs = "CREATE TABLE IF NOT EXISTS {$table_jobs} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            job_id varchar(32) NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            prompt_hash varchar(64) DEFAULT NULL,
            content_type varchar(20) NOT NULL DEFAULT 'post',
            result_data longtext DEFAULT NULL,
            error_message text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            started_at datetime DEFAULT NULL,
            completed_at datetime DEFAULT NULL,
            post_id bigint(20) UNSIGNED DEFAULT NULL,
            metadata longtext DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY job_id (job_id),
            KEY status (status),
            KEY user_id (user_id),
            KEY created_at (created_at),
            KEY post_id (post_id)
        ) {$charset_collate};";

        // Tabela de logs estruturados
        $sql_logs = "CREATE TABLE IF NOT EXISTS {$table_logs} (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            level varchar(10) NOT NULL,
            component varchar(50) NOT NULL,
            event varchar(50) NOT NULL,
            request_id varchar(32) DEFAULT NULL,
            user_id bigint(20) UNSIGNED DEFAULT NULL,
            method varchar(10) DEFAULT NULL,
            endpoint varchar(255) DEFAULT NULL,
            duration_ms int(11) DEFAULT NULL,
            status_code smallint(5) DEFAULT NULL,
            tokens_input int(11) DEFAULT NULL,
            tokens_output int(11) DEFAULT NULL,
            error text DEFAULT NULL,
            context longtext DEFAULT NULL,
            PRIMARY KEY (id),
            KEY timestamp (timestamp),
            KEY level (level),
            KEY component (component),
            KEY event (event),
            KEY user_id (user_id),
            KEY request_id (request_id)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_jobs);
        dbDelta($sql_logs);

        // Registra versão do schema
        update_option('aicg_db_version', AICG_VERSION);
    }

    /**
     * Registra opções padrão do plugin
     *
     * @return void
     */
    private static function register_default_options(): void
    {
        $default_options = [
            'aicg_version' => AICG_VERSION,
            'aicg_db_version' => AICG_VERSION,
            'aicg_default_model' => 'llama-3.3-70b-versatile',
            'aicg_default_tone' => 'professional',
            'aicg_default_length' => 'medium',
            'aicg_include_images' => true,
            'aicg_cache_enabled' => true,
            'aicg_cache_ttl' => DAY_IN_SECONDS,
            'aicg_async_generation' => true,
            'aicg_log_retention_days' => 90,
            'aicg_enable_notifications' => true,
            'aicg_rate_limit_user_minute' => 10,
            'aicg_rate_limit_user_hour' => 50,
            'aicg_rate_limit_ip_minute' => 5,
            'aicg_rate_limit_global_hour' => 1000,
        ];

        foreach ($default_options as $option_name => $default_value) {
            if (get_option($option_name) === false) {
                add_option($option_name, $default_value, '', false);
            }
        }
    }

    /**
     * Cria diretório de uploads dedicado
     *
     * @return void
     */
    private static function create_upload_directory(): void
    {
        $upload_dir = wp_upload_dir();
        $aicg_dir = $upload_dir['basedir'] . '/aicg';

        if (!file_exists($aicg_dir)) {
            wp_mkdir_p($aicg_dir);
        }

        // Cria arquivo .htaccess para proteção
        $htaccess_file = $aicg_dir . '/.htaccess';
        if (!file_exists($htaccess_file)) {
            $htaccess_content = "Options -Indexes\n";
            $htaccess_content .= "deny from all\n";
            file_put_contents($htaccess_file, $htaccess_content);
        }

        // Cria arquivo index.php vazio
        $index_file = $aicg_dir . '/index.php';
        if (!file_exists($index_file)) {
            file_put_contents($index_file, '<?php // Silence is golden');
        }
    }

    /**
     * Agenda eventos cron
     *
     * @return void
     */
    private static function schedule_cron_events(): void
    {
        // Limpeza de logs antigos (diariamente)
        if (!wp_next_scheduled('aicg_cleanup_logs')) {
            wp_schedule_event(time(), 'daily', 'aicg_cleanup_logs');
        }

        // Sincronização de modelos disponíveis (semanalmente)
        if (!wp_next_scheduled('aicg_sync_models')) {
            wp_schedule_event(time(), 'weekly', 'aicg_sync_models');
        }

        // Processamento de jobs pendentes (a cada 2 minutos)
        if (!wp_next_scheduled('aicg_process_pending_jobs')) {
            wp_schedule_event(time(), 'aicg_every_2_minutes', 'aicg_process_pending_jobs');
        }
    }

    /**
     * Obtém a versão atual do schema do banco de dados
     *
     * @return string
     */
    public static function get_db_version(): string
    {
        return get_option('aicg_db_version', '0.0.0');
    }

    /**
     * Verifica se o banco de dados precisa ser atualizado
     *
     * @return bool
     */
    public static function needs_db_update(): bool
    {
        return version_compare(self::get_db_version(), AICG_VERSION, '<');
    }
}
