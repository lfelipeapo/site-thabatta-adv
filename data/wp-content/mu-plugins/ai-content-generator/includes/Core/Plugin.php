<?php
/**
 * Classe principal de inicialização do plugin
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
 * Class Plugin
 *
 * Gerencia a inicialização e configuração central do plugin
 *
 * @package AICG\Core
 * @since   1.0.0
 */
class Plugin
{
    /**
     * Instância única
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * Obtém instância única
     *
     * @return Plugin
     */
    public static function get_instance(): Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->setup_hooks();
    }

    /**
     * Configura hooks do plugin
     *
     * @return void
     */
    private function setup_hooks(): void
    {
        // Inicialização
        add_action('init', [$this, 'init']);
        
        // Registro de post types e taxonomias
        add_action('init', [$this, 'register_post_types'], 10);
        add_action('init', [$this, 'register_taxonomies'], 11);
        
        // Customizer
        add_action('customize_register', [$this, 'customize_register']);
        
        // Notificações de publicação
        add_action('publish_future_post', [$this, 'notify_on_publish']);
        
        // Hooks admin
        if (is_admin()) {
            add_action('admin_init', [$this, 'admin_init']);
            add_filter('plugin_action_links_' . plugin_basename(AICG_PLUGIN_FILE), [$this, 'add_action_links']);
        }
    }

    /**
     * Inicialização geral
     *
     * @return void
     */
    public function init(): void
    {
        // Registra capabilities personalizadas
        $this->register_capabilities();
    }

    /**
     * Inicialização do admin
     *
     * @return void
     */
    public function admin_init(): void
    {
        // Registra configurações
        $this->register_settings();
    }

    /**
     * Registra capabilities personalizadas
     *
     * @return void
     */
    private function register_capabilities(): void
    {
        $roles = ['administrator', 'editor', 'author'];
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            
            if (!$role) {
                continue;
            }

            // Capability para gerar conteúdo
            if ($role_name === 'administrator' || $role_name === 'editor') {
                $role->add_cap('aicg_generate_content');
            }

            // Capability para configurar API (apenas admin)
            if ($role_name === 'administrator') {
                $role->add_cap('aicg_manage_settings');
            }
        }
    }

    /**
     * Registra configurações do plugin
     *
     * @return void
     */
    private function register_settings(): void
    {
        // Seção de API
        register_setting('aicg_api_settings', 'aicg_api_key_encrypted', [
            'type' => 'string',
            'description' => 'Chave API Groq criptografada',
            'sanitize_callback' => [$this, 'sanitize_api_key'],
            'autoload' => false,
        ]);

        register_setting('aicg_api_settings', 'aicg_default_model', [
            'type' => 'string',
            'default' => 'llama-3.3-70b-versatile',
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        // Seção de preferências
        register_setting('aicg_preferences', 'aicg_default_tone', [
            'type' => 'string',
            'default' => 'professional',
            'sanitize_callback' => 'sanitize_key',
        ]);

        register_setting('aicg_preferences', 'aicg_default_length', [
            'type' => 'string',
            'default' => 'medium',
            'sanitize_callback' => 'sanitize_key',
        ]);

        register_setting('aicg_preferences', 'aicg_include_images', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ]);

        // Configurações avançadas
        register_setting('aicg_advanced', 'aicg_cache_enabled', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ]);

        register_setting('aicg_advanced', 'aicg_cache_ttl', [
            'type' => 'integer',
            'default' => DAY_IN_SECONDS,
            'sanitize_callback' => 'absint',
        ]);

        register_setting('aicg_advanced', 'aicg_async_generation', [
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ]);

        register_setting('aicg_advanced', 'aicg_log_retention_days', [
            'type' => 'integer',
            'default' => 90,
            'sanitize_callback' => 'absint',
        ]);
    }

    /**
     * Sanitiza a chave API
     *
     * @param string $value Valor da chave
     * @return string
     */
    public function sanitize_api_key(string $value): string
    {
        // Se estiver vazio, mantém o valor atual
        if (empty($value)) {
            return get_option('aicg_api_key_encrypted', '');
        }

        // Criptografa a chave antes de salvar
        $encryption = new \AICG\Security\Encryption();
        return $encryption->encrypt($value);
    }

    /**
     * Registra Custom Post Type para conteúdo IA
     *
     * @return void
     */
    public function register_post_types(): void
    {
        // Verifica se o CPT deve ser registrado (configuração opcional)
        if (!get_option('aicg_enable_cpt', false)) {
            return;
        }

        $labels = [
            'name' => esc_html__('Conteúdo IA', 'ai-content-generator'),
            'singular_name' => esc_html__('Conteúdo IA', 'ai-content-generator'),
            'add_new' => esc_html__('Adicionar Novo', 'ai-content-generator'),
            'add_new_item' => esc_html__('Adicionar Novo Conteúdo IA', 'ai-content-generator'),
            'edit_item' => esc_html__('Editar Conteúdo IA', 'ai-content-generator'),
            'new_item' => esc_html__('Novo Conteúdo IA', 'ai-content-generator'),
            'view_item' => esc_html__('Ver Conteúdo IA', 'ai-content-generator'),
            'search_items' => esc_html__('Buscar Conteúdo IA', 'ai-content-generator'),
            'not_found' => esc_html__('Nenhum conteúdo IA encontrado', 'ai-content-generator'),
            'not_found_in_trash' => esc_html__('Nenhum conteúdo IA na lixeira', 'ai-content-generator'),
        ];

        $args = [
            'labels' => $labels,
            'description' => esc_html__('Conteúdo gerado automaticamente por inteligência artificial', 'ai-content-generator'),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-art',
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
            'has_archive' => false,
            'exclude_from_search' => true,
            'show_in_rest' => true,
            'rest_base' => 'ai-content',
        ];

        register_post_type('ai_content', $args);
    }

    /**
     * Registra taxonomias customizadas
     *
     * @return void
     */
    public function register_taxonomies(): void
    {
        // Taxonomia de tópicos (hierárquica)
        register_taxonomy('ai_topic', ['ai_content', 'post', 'page'], [
            'labels' => [
                'name' => esc_html__('Tópicos IA', 'ai-content-generator'),
                'singular_name' => esc_html__('Tópico IA', 'ai-content-generator'),
            ],
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
        ]);

        // Taxonomia de status de revisão (não-hierárquica)
        register_taxonomy('ai_review_status', ['ai_content', 'post', 'page'], [
            'labels' => [
                'name' => esc_html__('Status de Revisão', 'ai-content-generator'),
                'singular_name' => esc_html__('Status de Revisão', 'ai-content-generator'),
            ],
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
        ]);

        // Registra terms padrão para status de revisão
        if (!get_option('aicg_default_terms_registered')) {
            $default_terms = [
                'pending_review' => esc_html__('Aguardando Revisão', 'ai-content-generator'),
                'approved' => esc_html__('Aprovado', 'ai-content-generator'),
                'needs_revision' => esc_html__('Precisa de Revisão', 'ai-content-generator'),
                'rejected' => esc_html__('Rejeitado', 'ai-content-generator'),
            ];

            foreach ($default_terms as $slug => $name) {
                if (!term_exists($slug, 'ai_review_status')) {
                    wp_insert_term($name, 'ai_review_status', ['slug' => $slug]);
                }
            }

            update_option('aicg_default_terms_registered', true);
        }
    }

    /**
     * Registra seções no Customizer
     *
     * @param \WP_Customize_Manager $wp_customize Instância do customizer
     * @return void
     */
    public function customize_register(\WP_Customize_Manager $wp_customize): void
    {
        // Seção principal
        $wp_customize->add_section('aicg_defaults', [
            'title' => esc_html__('Gerador de Conteúdo IA', 'ai-content-generator'),
            'priority' => 200,
        ]);

        // Tom de voz padrão
        $wp_customize->add_setting('aicg_default_tone', [
            'default' => 'professional',
            'sanitize_callback' => 'sanitize_key',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('aicg_default_tone', [
            'section' => 'aicg_defaults',
            'label' => esc_html__('Tom de voz padrão', 'ai-content-generator'),
            'type' => 'select',
            'choices' => [
                'professional' => esc_html__('Profissional', 'ai-content-generator'),
                'casual' => esc_html__('Casual/Conversacional', 'ai-content-generator'),
                'technical' => esc_html__('Técnico/Especializado', 'ai-content-generator'),
                'persuasive' => esc_html__('Persuasivo/Vendas', 'ai-content-generator'),
                'narrative' => esc_html__('Narrativo/Storytelling', 'ai-content-generator'),
            ],
        ]);

        // Comprimento padrão
        $wp_customize->add_setting('aicg_default_length', [
            'default' => 'medium',
            'sanitize_callback' => 'sanitize_key',
        ]);

        $wp_customize->add_control('aicg_default_length', [
            'section' => 'aicg_defaults',
            'label' => esc_html__('Comprimento padrão', 'ai-content-generator'),
            'type' => 'radio',
            'choices' => [
                'short' => esc_html__('Curto (300-500 palavras)', 'ai-content-generator'),
                'medium' => esc_html__('Médio (800-1200 palavras)', 'ai-content-generator'),
                'long' => esc_html__('Longo (1500-2500 palavras)', 'ai-content-generator'),
            ],
        ]);
    }

    /**
     * Envia notificação quando conteúdo agendado é publicado
     *
     * @param int $post_id ID do post
     * @return void
     */
    public function notify_on_publish(int $post_id): void
    {
        // Verifica se é conteúdo gerado por IA
        if (!get_post_meta($post_id, '_aicg_generated', true)) {
            return;
        }

        // Verifica se notificações estão habilitadas
        if (!get_option('aicg_enable_notifications', true)) {
            return;
        }

        $post = get_post($post_id);
        if (!$post) {
            return;
        }

        $author = get_userdata($post->post_author);
        if (!$author) {
            return;
        }

        $subject = sprintf(
            /* translators: %s: Site name */
            esc_html__('[%s] Seu conteúdo IA foi publicado', 'ai-content-generator'),
            get_bloginfo('name')
        );

        $message = sprintf(
            /* translators: 1: Post title, 2: Edit link */
            esc_html__("Olá,%s\n\nO post \"%s\" agendado foi publicado automaticamente.\n\nVocê pode editá-lo aqui: %s\n\nAtenciosamente,\n%s", 'ai-content-generator'),
            "\n",
            get_the_title($post_id),
            get_edit_post_link($post_id, 'raw'),
            get_bloginfo('name')
        );

        wp_mail($author->user_email, $subject, $message);
    }

    /**
     * Adiciona links de ação na lista de plugins
     *
     * @param array $links Links existentes
     * @return array
     */
    public function add_action_links(array $links): array
    {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url('admin.php?page=ai-content-generator-settings'),
            esc_html__('Configurações', 'ai-content-generator')
        );

        array_unshift($links, $settings_link);
        return $links;
    }
}
