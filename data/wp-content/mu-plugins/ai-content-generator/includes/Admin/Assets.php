<?php
/**
 * Gerenciamento de assets (CSS/JS)
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
 * Class Assets
 *
 * @package AICG\Admin
 * @since   1.0.0
 */
class Assets
{
    /**
     * Construtor
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * Enfileira assets do admin
     *
     * @param string $hook Página atual
     * @return void
     */
    public function enqueue_admin_assets(string $hook): void
    {
        // Verifica se estamos em uma página do plugin
        if (!$this->is_plugin_page($hook)) {
            return;
        }

        // Página principal (React)
        if ($this->is_main_generator_page($hook)) {
            $this->enqueue_react_app();
        } else {
            // Outras páginas (CSS/JS legado)
            $this->enqueue_legacy_assets();
        }
    }

    /**
     * Verifica se é página do plugin
     *
     * @param string $hook Hook da página
     * @return bool
     */
    private function is_plugin_page(string $hook): bool
    {
        $plugin_pages = [
            'toplevel_page_ai-content-generator',
            'ai-content_page_ai-content-generator-history',
            'ai-content_page_ai-content-generator-settings',
        ];

        return in_array($hook, $plugin_pages, true);
    }

    /**
     * Verifica se é página principal do gerador
     *
     * @param string $hook Hook da página
     * @return bool
     */
    private function is_main_generator_page(string $hook): bool
    {
        return $hook === 'toplevel_page_ai-content-generator' && !isset($_GET['onboarding']);
    }

    /**
     * Enfileira aplicação React
     *
     * @return void
     */
    private function enqueue_react_app(): void
    {
        $asset_file = AICG_PLUGIN_DIR . 'build/index.asset.php';

        // Se build não existe, usa modo desenvolvimento
        if (!file_exists($asset_file)) {
            $this->enqueue_development_assets();
            return;
        }

        $asset = require $asset_file;

        // Script principal
        wp_enqueue_script(
            'aicg-admin-app',
            AICG_PLUGIN_URL . 'build/index.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );

        // CSS
        if (file_exists(AICG_PLUGIN_DIR . 'build/index.css')) {
            wp_enqueue_style(
                'aicg-admin-app',
                AICG_PLUGIN_URL . 'build/index.css',
                [],
                $asset['version']
            );
        }

        // Dados localizados
        $this->localize_script('aicg-admin-app');
    }

    /**
     * Enfileira assets de desenvolvimento
     *
     * @return void
     */
    private function enqueue_development_assets(): void
    {
        // Dependencies básicas do WordPress
        $dependencies = [
            'wp-element',
            'wp-components',
            'wp-api-fetch',
            'wp-i18n',
            'wp-url',
            'wp-hooks',
            'wp-data',
        ];

        foreach ($dependencies as $handle) {
            wp_enqueue_script($handle);
        }

        // CSS do WordPress
        wp_enqueue_style('wp-components');

        // Adiciona aviso de desenvolvimento
        wp_add_inline_script(
            'wp-api-fetch',
            'console.warn("AICG: Modo desenvolvimento - execute npm run build para gerar assets de produção");'
        );

        $this->localize_script('wp-api-fetch');
    }

    /**
     * Enfileira assets legados
     *
     * @return void
     */
    private function enqueue_legacy_assets(): void
    {
        // CSS
        wp_enqueue_style(
            'aicg-admin-styles',
            AICG_PLUGIN_URL . 'assets/css/admin.css',
            [],
            AICG_VERSION
        );

        // JS
        wp_enqueue_script(
            'aicg-admin-scripts',
            AICG_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            AICG_VERSION,
            true
        );

        $this->localize_script('aicg-admin-scripts');
    }

    /**
     * Localiza script com dados do servidor
     *
     * @param string $handle Handle do script
     * @return void
     */
    private function localize_script(string $handle): void
    {
        wp_localize_script($handle, 'aicgData', [
            'restUrl' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
            'apiStatus' => $this->get_api_status(),
            'userPrefs' => $this->get_user_preferences(),
            'i18n' => $this->get_translations(),
            'pluginUrl' => AICG_PLUGIN_URL,
            'version' => AICG_VERSION,
        ]);
    }

    /**
     * Obtém status da API
     *
     * @return array
     */
    private function get_api_status(): array
    {
        $api_key_configured = !empty(get_option('aicg_api_key_encrypted'));

        return [
            'configured' => $api_key_configured,
            'defaultModel' => get_option('aicg_default_model', 'llama-3.3-70b-versatile'),
            'defaultTone' => get_option('aicg_default_tone', 'professional'),
            'defaultLength' => get_option('aicg_default_length', 'medium'),
        ];
    }

    /**
     * Obtém preferências do usuário
     *
     * @return array
     */
    private function get_user_preferences(): array
    {
        $user_id = get_current_user_id();

        return [
            'defaultContentType' => get_user_meta($user_id, 'aicg_pref_content_type', true) ?: 'post',
            'recentPrompts' => get_user_meta($user_id, 'aicg_recent_prompts', true) ?: [],
        ];
    }

    /**
     * Obtém traduções para JavaScript
     *
     * @return array
     */
    private function get_translations(): array
    {
        return [
            'generateContent' => esc_html__('Gerar Conteúdo', 'ai-content-generator'),
            'promptPlaceholder' => esc_html__('Descreva o conteúdo que você quer gerar...', 'ai-content-generator'),
            'contentType' => esc_html__('Tipo de Conteúdo', 'ai-content-generator'),
            'post' => esc_html__('Post', 'ai-content-generator'),
            'page' => esc_html__('Página', 'ai-content-generator'),
            'tone' => esc_html__('Tom de Voz', 'ai-content-generator'),
            'professional' => esc_html__('Profissional', 'ai-content-generator'),
            'casual' => esc_html__('Casual', 'ai-content-generator'),
            'technical' => esc_html__('Técnico', 'ai-content-generator'),
            'persuasive' => esc_html__('Persuasivo', 'ai-content-generator'),
            'narrative' => esc_html__('Narrativo', 'ai-content-generator'),
            'length' => esc_html__('Comprimento', 'ai-content-generator'),
            'short' => esc_html__('Curto (300-500 palavras)', 'ai-content-generator'),
            'medium' => esc_html__('Médio (800-1200 palavras)', 'ai-content-generator'),
            'long' => esc_html__('Longo (1500-2500 palavras)', 'ai-content-generator'),
            'schedule' => esc_html__('Agendar Publicação', 'ai-content-generator'),
            'includeImage' => esc_html__('Incluir Imagem Destacada', 'ai-content-generator'),
            'generating' => esc_html__('Gerando conteúdo...', 'ai-content-generator'),
            'preview' => esc_html__('Preview', 'ai-content-generator'),
            'edit' => esc_html__('Editar', 'ai-content-generator'),
            'publish' => esc_html__('Publicar', 'ai-content-generator'),
            'saveDraft' => esc_html__('Salvar Rascunho', 'ai-content-generator'),
            'cancel' => esc_html__('Cancelar', 'ai-content-generator'),
            'error' => esc_html__('Erro', 'ai-content-generator'),
            'success' => esc_html__('Sucesso', 'ai-content-generator'),
        ];
    }
}
