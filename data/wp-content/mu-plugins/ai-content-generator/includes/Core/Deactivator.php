<?php
/**
 * Classe de desativação do plugin
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
 * Class Deactivator
 *
 * Gerencia a desativação e desinstalação do plugin
 *
 * @package AICG\Core
 * @since   1.0.0
 */
class Deactivator
{
    /**
     * Executa a desativação do plugin
     *
     * @return void
     */
    public static function deactivate(): void
    {
        // Remove eventos cron agendados
        self::clear_scheduled_events();

        // Limpa transients
        self::clear_transients();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Executa a desinstalação completa do plugin
     *
     * @return void
     */
    public static function uninstall(): void
    {
        // Verifica se deve limpar todos os dados
        if (!get_option('aicg_preserve_data_on_uninstall', false)) {
            self::delete_all_data();
        }
    }

    /**
     * Remove eventos cron agendados
     *
     * @return void
     */
    private static function clear_scheduled_events(): void
    {
        $hooks = [
            'aicg_cleanup_logs',
            'aicg_sync_models',
            'aicg_process_pending_jobs',
            'aicg_process_job',
        ];

        foreach ($hooks as $hook) {
            $timestamp = wp_next_scheduled($hook);
            if ($timestamp) {
                wp_unschedule_event($timestamp, $hook);
            }
        }
    }

    /**
     * Limpa transients do plugin
     *
     * @return void
     */
    private static function clear_transients(): void
    {
        global $wpdb;

        // Remove transients relacionados ao plugin
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_aicg_%',
                '_transient_timeout_aicg_%'
            )
        );
    }

    /**
     * Remove todos os dados do plugin
     *
     * @return void
     */
    private static function delete_all_data(): void
    {
        global $wpdb;

        // Remove tabelas customizadas
        $tables = [
            $wpdb->prefix . 'aicg_jobs',
            $wpdb->prefix . 'aicg_logs',
        ];

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }

        // Remove todas as opções do plugin
        $options = [
            'aicg_version',
            'aicg_db_version',
            'aicg_api_key_encrypted',
            'aicg_default_model',
            'aicg_default_tone',
            'aicg_default_length',
            'aicg_include_images',
            'aicg_cache_enabled',
            'aicg_cache_ttl',
            'aicg_async_generation',
            'aicg_log_retention_days',
            'aicg_enable_notifications',
            'aicg_enable_cpt',
            'aicg_preserve_data_on_uninstall',
            'aicg_onboarding_completed',
            'aicg_onboarding_redirect',
            'aicg_rate_limit_user_minute',
            'aicg_rate_limit_user_hour',
            'aicg_rate_limit_ip_minute',
            'aicg_rate_limit_global_hour',
            'aicg_default_terms_registered',
        ];

        foreach ($options as $option) {
            delete_option($option);
        }

        // Remove postmeta relacionada
        $wpdb->query(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_aicg_%'"
        );

        // Remove capabilities personalizadas
        self::remove_capabilities();

        // Remove diretório de uploads (opcional)
        self::remove_upload_directory();
    }

    /**
     * Remove capabilities personalizadas
     *
     * @return void
     */
    private static function remove_capabilities(): void
    {
        $roles = ['administrator', 'editor', 'author'];
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            
            if (!$role) {
                continue;
            }

            $role->remove_cap('aicg_generate_content');
            $role->remove_cap('aicg_manage_settings');
        }
    }

    /**
     * Remove diretório de uploads
     *
     * @return void
     */
    private static function remove_upload_directory(): void
    {
        $upload_dir = wp_upload_dir();
        $aicg_dir = $upload_dir['basedir'] . '/aicg';

        if (is_dir($aicg_dir)) {
            // Remove arquivos internos
            $files = glob($aicg_dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            
            // Remove diretório
            @rmdir($aicg_dir);
        }
    }
}
