<?php
/**
 * Classe AdminManager para gerenciamento de interface administrativa
 * 
 * Responsável por integrar com o Customizer, Settings API, Classic Editor,
 * Jetpack e Jetpack Boost. Inclui suporte à criação de páginas de admin
 * modulares via interface com Customizer.
 * 
 * @package WPFramework\Core
 */

namespace WPFramework\Core;

class AdminManager
{
    /**
     * Instância singleton
     * 
     * @var AdminManager
     */
    private static $instance = null;
    
    /**
     * Páginas de admin registradas
     * 
     * @var array
     */
    private $adminPages = [];
    
    /**
     * Seções de configurações registradas
     * 
     * @var array
     */
    private $settingsSections = [];
    
    /**
     * Campos de configurações registrados
     * 
     * @var array
     */
    private $settingsFields = [];

    /**
     * Inicializa o AdminManager
     * 
     * @return AdminManager
     */
    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Construtor privado para padrão singleton
     */
    private function __construct()
    {
        // Registra hooks para admin
        add_action('admin_menu', [$this, 'registerAdminPages']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('customize_register', [$this, 'registerCustomizer']);
        
        // Integração com Classic Editor
        add_action('admin_init', [$this, 'setupClassicEditor']);
        
        // Integração com Jetpack
        add_action('init', [$this, 'setupJetpack']);
        
        // Carrega componentes de admin
        $this->loadAdminComponents();
    }

    /**
     * Carrega componentes de admin
     */
    private function loadAdminComponents()
    {
        // Carrega automaticamente todos os componentes de admin
        $adminDir = WPFRAMEWORK_APP_DIR . '/Core/Admin';
        
        if (is_dir($adminDir)) {
            $files = glob($adminDir . '/*.php');
            
            foreach ($files as $file) {
                require_once $file;
                
                // Extrai o nome da classe do arquivo
                $className = basename($file, '.php');
                $fullClassName = '\\WPFramework\\Core\\Admin\\' . $className;
                
                // Inicializa a classe se existir e tiver método init
                if (class_exists($fullClassName) && method_exists($fullClassName, 'init')) {
                    call_user_func([$fullClassName, 'init']);
                }
            }
        }
    }

    /**
     * Adiciona uma página de admin
     * 
     * @param array $page Configurações da página
     * @return AdminManager
     */
    public function addAdminPage($page)
    {
        $this->adminPages[] = $page;
        
        return $this;
    }

    /**
     * Registra páginas de admin
     */
    public function registerAdminPages()
    {
        foreach ($this->adminPages as $page) {
            // Valores padrão
            $defaults = [
                'parent_slug' => '',
                'page_title' => '',
                'menu_title' => '',
                'capability' => 'manage_options',
                'menu_slug' => '',
                'callback' => '',
                'icon_url' => '',
                'position' => null
            ];
            
            // Mescla com os valores fornecidos
            $page = array_merge($defaults, $page);
            
            // Registra a página
            if (empty($page['parent_slug'])) {
                add_menu_page(
                    $page['page_title'],
                    $page['menu_title'],
                    $page['capability'],
                    $page['menu_slug'],
                    $page['callback'],
                    $page['icon_url'],
                    $page['position']
                );
            } else {
                add_submenu_page(
                    $page['parent_slug'],
                    $page['page_title'],
                    $page['menu_title'],
                    $page['capability'],
                    $page['menu_slug'],
                    $page['callback']
                );
            }
        }
    }

    /**
     * Adiciona uma seção de configurações
     * 
     * @param array $section Configurações da seção
     * @return AdminManager
     */
    public function addSettingsSection($section)
    {
        $this->settingsSections[] = $section;
        
        return $this;
    }

    /**
     * Adiciona um campo de configurações
     * 
     * @param array $field Configurações do campo
     * @return AdminManager
     */
    public function addSettingsField($field)
    {
        $this->settingsFields[] = $field;
        
        return $this;
    }

    /**
     * Registra configurações
     */
    public function registerSettings()
    {
        // Registra seções
        foreach ($this->settingsSections as $section) {
            // Valores padrão
            $defaults = [
                'id' => '',
                'title' => '',
                'callback' => '',
                'page' => ''
            ];
            
            // Mescla com os valores fornecidos
            $section = array_merge($defaults, $section);
            
            // Registra a seção
            add_settings_section(
                $section['id'],
                $section['title'],
                $section['callback'],
                $section['page']
            );
        }
        
        // Registra campos
        foreach ($this->settingsFields as $field) {
            // Valores padrão
            $defaults = [
                'id' => '',
                'title' => '',
                'callback' => '',
                'page' => '',
                'section' => '',
                'args' => []
            ];
            
            // Mescla com os valores fornecidos
            $field = array_merge($defaults, $field);
            
            // Registra o campo
            add_settings_field(
                $field['id'],
                $field['title'],
                $field['callback'],
                $field['page'],
                $field['section'],
                $field['args']
            );
            
            // Registra a configuração
            register_setting($field['page'], $field['id']);
        }
    }

    /**
     * Registra configurações no Customizer
     * 
     * @param \WP_Customize_Manager $wp_customize Objeto do Customizer
     */
    public function registerCustomizer($wp_customize)
    {
        // Carrega automaticamente todos os componentes do Customizer
        $customizerDir = WPFRAMEWORK_APP_DIR . '/Core/Admin/Customizer';
        
        if (is_dir($customizerDir)) {
            $files = glob($customizerDir . '/*.php');
            
            foreach ($files as $file) {
                require_once $file;
                
                // Extrai o nome da classe do arquivo
                $className = basename($file, '.php');
                $fullClassName = '\\WPFramework\\Core\\Admin\\Customizer\\' . $className;
                
                // Inicializa a classe se existir e tiver método register
                if (class_exists($fullClassName) && method_exists($fullClassName, 'register')) {
                    call_user_func([$fullClassName, 'register'], $wp_customize);
                }
            }
        }
    }

    /**
     * Configura o Classic Editor
     */
    public function setupClassicEditor()
    {
        // Verifica se o plugin Classic Editor está ativo
        if (!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        if (is_plugin_active('classic-editor/classic-editor.php')) {
            // Adiciona suporte a recursos adicionais no editor
            add_filter('tiny_mce_before_init', [$this, 'extendTinyMCE']);
            
            // Adiciona botões adicionais
            add_filter('mce_buttons', [$this, 'registerTinyMCEButtons']);
            add_filter('mce_external_plugins', [$this, 'registerTinyMCEPlugins']);
        }
    }

    /**
     * Estende o TinyMCE
     * 
     * @param array $settings Configurações do TinyMCE
     * @return array
     */
    public function extendTinyMCE($settings)
    {
        // Adiciona formatos personalizados
        $settings['style_formats_merge'] = true;
        
        $style_formats = [
            [
                'title' => 'Botões',
                'items' => [
                    [
                        'title' => 'Botão Primário',
                        'selector' => 'a',
                        'classes' => 'btn btn-primary'
                    ],
                    [
                        'title' => 'Botão Secundário',
                        'selector' => 'a',
                        'classes' => 'btn btn-secondary'
                    ]
                ]
            ],
            [
                'title' => 'Caixas',
                'items' => [
                    [
                        'title' => 'Caixa de Alerta',
                        'block' => 'div',
                        'classes' => 'alert alert-warning',
                        'wrapper' => true
                    ],
                    [
                        'title' => 'Caixa de Informação',
                        'block' => 'div',
                        'classes' => 'alert alert-info',
                        'wrapper' => true
                    ]
                ]
            ]
        ];
        
        $settings['style_formats'] = json_encode($style_formats);
        
        return $settings;
    }

    /**
     * Registra botões adicionais no TinyMCE
     * 
     * @param array $buttons Botões do TinyMCE
     * @return array
     */
    public function registerTinyMCEButtons($buttons)
    {
        $buttons[] = 'styleselect';
        
        return $buttons;
    }

    /**
     * Registra plugins adicionais no TinyMCE
     * 
     * @param array $plugins Plugins do TinyMCE
     * @return array
     */
    public function registerTinyMCEPlugins($plugins)
    {
        // Adiciona plugins personalizados
        $plugins['wpframework_tinymce'] = WPFRAMEWORK_URI . '/public/js/admin/tinymce-plugins.js';
        
        return $plugins;
    }

    /**
     * Configura o Jetpack
     */
    public function setupJetpack()
    {
        // Verifica se o Jetpack está ativo
        if (class_exists('Jetpack')) {
            // Adiciona suporte a recursos do Jetpack
            add_theme_support('infinite-scroll', [
                'container' => 'main',
                'render' => [$this, 'infiniteScrollRender'],
                'footer' => 'page',
            ]);
            
            // Adiciona suporte a recursos do Jetpack Boost
            add_theme_support('jetpack-boost-critical-css');
            add_theme_support('jetpack-boost-lazy-images');
            
            // Adiciona suporte a recursos do Content Options
            add_theme_support('jetpack-content-options', [
                'post-details' => [
                    'stylesheet' => 'wpframework-style',
                    'date' => '.posted-on',
                    'categories' => '.cat-links',
                    'tags' => '.tags-links',
                    'author' => '.byline',
                    'comment' => '.comments-link',
                ],
                'featured-images' => [
                    'archive' => true,
                    'post' => true,
                    'page' => true,
                ],
            ]);
        }
    }

    /**
     * Renderiza itens do Infinite Scroll
     */
    public function infiniteScrollRender()
    {
        while (have_posts()) {
            the_post();
            get_template_part('app/Views/partials/content', get_post_type());
        }
    }

    /**
     * Cria uma página de admin modular
     * 
     * @param string $title Título da página
     * @param string $slug Slug da página
     * @param array $modules Módulos da página
     * @param string $icon Ícone da página
     * @param int $position Posição no menu
     * @return AdminManager
     */
    public function createModularPage($title, $slug, $modules = [], $icon = 'dashicons-admin-generic', $position = null)
    {
        // Adiciona a página de admin
        $this->addAdminPage([
            'page_title' => $title,
            'menu_title' => $title,
            'capability' => 'manage_options',
            'menu_slug' => $slug,
            'callback' => function() use ($title, $slug, $modules) {
                // Renderiza a página modular
                echo '<div class="wrap">';
                echo '<h1>' . esc_html($title) . '</h1>';
                
                // Renderiza os módulos
                echo '<div class="wpframework-admin-modules">';
                
                foreach ($modules as $module) {
                    // Valores padrão
                    $defaults = [
                        'title' => '',
                        'description' => '',
                        'callback' => '',
                        'icon' => 'dashicons-admin-generic'
                    ];
                    
                    // Mescla com os valores fornecidos
                    $module = array_merge($defaults, $module);
                    
                    // Renderiza o módulo
                    echo '<div class="wpframework-admin-module">';
                    echo '<div class="wpframework-admin-module-header">';
                    echo '<span class="dashicons ' . esc_attr($module['icon']) . '"></span>';
                    echo '<h2>' . esc_html($module['title']) . '</h2>';
                    echo '</div>';
                    
                    echo '<div class="wpframework-admin-module-content">';
                    
                    if (!empty($module['description'])) {
                        echo '<p>' . esc_html($module['description']) . '</p>';
                    }
                    
                    if (is_callable($module['callback'])) {
                        call_user_func($module['callback']);
                    }
                    
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
            },
            'icon_url' => $icon,
            'position' => $position
        ]);
        
        return $this;
    }

    /**
     * Adiciona uma página de configurações ACF
     * 
     * @param string $title Título da página
     * @param string $slug Slug da página
     * @param string $parent_slug Slug da página pai
     * @return AdminManager
     */
    public function addACFOptionsPage($title, $slug, $parent_slug = '')
    {
        // Verifica se o ACF está ativo
        if (function_exists('acf_add_options_page')) {
            $args = [
                'page_title' => $title,
                'menu_title' => $title,
                'menu_slug' => $slug,
                'capability' => 'manage_options',
                'redirect' => false
            ];
            
            if (!empty($parent_slug)) {
                $args['parent_slug'] = $parent_slug;
            }
            
            acf_add_options_page($args);
        }
        
        return $this;
    }
}
