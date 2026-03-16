<?php
/**
 * Gerenciamento de menus administrativos
 *
 * @package AICG\Admin
 * @since   1.0.0
 */

namespace AICG\Admin;

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Menu
 *
 * @package AICG\Admin
 * @since   1.0.0
 */
class Menu
{
    /**
     * Construtor
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menus']);
        add_action('admin_init', [$this, 'handle_onboarding_redirect']);
    }

    /**
     * Registra menus administrativos
     *
     * @return void
     */
    public function register_menus(): void
    {
        // Menu principal
        add_menu_page(
            esc_html__('Gerador de Conteúdo IA', 'ai-content-generator'),
            esc_html__('Conteúdo IA', 'ai-content-generator'),
            'edit_posts',
            'ai-content-generator',
            [$this, 'render_generator_page'],
            'dashicons-art',
            25
        );

        // Submenu: Gerador
        add_submenu_page(
            'ai-content-generator',
            esc_html__('Gerar Conteúdo', 'ai-content-generator'),
            esc_html__('Gerar Conteúdo', 'ai-content-generator'),
            'edit_posts',
            'ai-content-generator',
            [$this, 'render_generator_page']
        );

        // Submenu: Histórico
        add_submenu_page(
            'ai-content-generator',
            esc_html__('Histórico de Geração', 'ai-content-generator'),
            esc_html__('Histórico', 'ai-content-generator'),
            'edit_posts',
            'ai-content-generator-history',
            [$this, 'render_history_page']
        );

        // Submenu: Configurações
        add_submenu_page(
            'ai-content-generator',
            esc_html__('Configurações', 'ai-content-generator'),
            esc_html__('Configurações', 'ai-content-generator'),
            'manage_options',
            'ai-content-generator-settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Redireciona para onboarding na primeira ativação
     *
     * @return void
     */
    public function handle_onboarding_redirect(): void
    {
        if (!get_option('aicg_onboarding_redirect')) {
            return;
        }

        delete_option('aicg_onboarding_redirect');

        if (!get_option('aicg_onboarding_completed')) {
            wp_safe_redirect(admin_url('admin.php?page=ai-content-generator&onboarding=1'));
            exit;
        }
    }

    /**
     * Renderiza página do gerador
     *
     * @return void
     */
    public function render_generator_page(): void
    {
        $onboarding = isset($_GET['onboarding']) ? true : false;
        
        if ($onboarding) {
            include AICG_PLUGIN_DIR . 'admin/views/onboarding.php';
        } else {
            echo '<div id="aicg-root" class="wrap"></div>';
        }
    }

    /**
     * Renderiza página de histórico
     *
     * @return void
     */
    public function render_history_page(): void
    {
        include AICG_PLUGIN_DIR . 'admin/views/history.php';
    }

    /**
     * Renderiza página de configurações
     *
     * @return void
     */
    public function render_settings_page(): void
    {
        include AICG_PLUGIN_DIR . 'admin/views/settings.php';
    }
}
