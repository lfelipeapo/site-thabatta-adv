<?php
/**
 * Classe Bootstrap para inicialização do framework
 * 
 * Responsável por inicializar todos os componentes do framework
 * e registrar hooks necessários no WordPress.
 * 
 * @package WPFramework\Core
 */

namespace WPFramework\Core;

class Bootstrap
{
    /**
     * Instância singleton
     * 
     * @var Bootstrap
     */
    private static $instance = null;

    /**
     * Inicializa o framework
     * 
     * @return Bootstrap
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
        // Registra hooks de inicialização
        add_action('after_setup_theme', [$this, 'setupTheme']);
        add_action('init', [$this, 'initFramework']);
        
        // Carrega componentes essenciais
        $this->loadCoreComponents();
    }

    /**
     * Configura recursos básicos do tema
     */
    public function setupTheme()
    {
        // Suporte a recursos do WordPress
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script'
        ]);
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('custom-logo');
        add_theme_support('custom-header');
        add_theme_support('custom-background');
        add_theme_support('editor-styles');
        add_theme_support('wp-block-styles');
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');
        
        // Registra menus
        register_nav_menus([
            'primary' => __('Menu Principal', 'wpframework'),
            'footer' => __('Menu Rodapé', 'wpframework')
        ]);
        
        // Carrega traduções
        load_theme_textdomain('wpframework', WPFRAMEWORK_DIR . '/languages');
    }

    /**
     * Inicializa componentes do framework
     */
    public function initFramework()
    {
        // Inicializa gerenciador de rotas
        Router::init();
        
        // Inicializa gerenciador de API REST
        ApiManager::init();
        
        // Inicializa gerenciador de sessão
        SessionManager::init();
        
        // Inicializa gerenciador de assets
        AssetManager::init();
        
        // Carrega Custom Post Types e Taxonomias
        $this->loadCustomPostTypes();
        
        // Inicializa componentes de admin
        if (is_admin()) {
            AdminManager::init();
        }
    }

    /**
     * Carrega componentes essenciais do core
     */
    private function loadCoreComponents()
    {
        // Carrega helpers globais
        require_once WPFRAMEWORK_APP_DIR . '/Core/Helpers/GlobalHelpers.php';
    }

    /**
     * Carrega Custom Post Types e Taxonomias
     */
    private function loadCustomPostTypes()
    {
        // Carrega todos os arquivos de CPT na pasta Models/PostTypes
        $postTypesDir = WPFRAMEWORK_APP_DIR . '/Models/PostTypes';
        
        if (is_dir($postTypesDir)) {
            $files = glob($postTypesDir . '/*.php');
            
            foreach ($files as $file) {
                require_once $file;
                
                // Extrai o nome da classe do arquivo
                $className = basename($file, '.php');
                $fullClassName = '\\WPFramework\\Models\\PostTypes\\' . $className;
                
                // Inicializa a classe se existir e tiver método register
                if (class_exists($fullClassName) && method_exists($fullClassName, 'register')) {
                    call_user_func([$fullClassName, 'register']);
                }
            }
        }
    }
}
