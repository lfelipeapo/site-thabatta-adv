<?php
/**
 * Arquivo principal do tema WPFramework
 * 
 * Este arquivo é mantido o mais enxuto possível, delegando a maior parte da
 * funcionalidade para a estrutura MVC orientada a objetos.
 * 
 * @package WPFramework
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

// Define constantes do tema
define('WPFRAMEWORK_VERSION', '1.0.0');
define('WPFRAMEWORK_DIR', get_template_directory());
define('WPFRAMEWORK_URI', get_template_directory_uri());
define('WPFRAMEWORK_APP_DIR', WPFRAMEWORK_DIR . '/app');
define('WPFRAMEWORK_PUBLIC_DIR', WPFRAMEWORK_DIR . '/public');

// Carrega o Composer Autoloader se existir
if (file_exists(WPFRAMEWORK_DIR . '/vendor/autoload.php')) {
    require_once WPFRAMEWORK_DIR . '/vendor/autoload.php';
} else {
    // Fallback para autoloader manual se o Composer não estiver disponível
    require_once WPFRAMEWORK_APP_DIR . '/Core/Autoloader.php';
    new \WPFramework\Core\Autoloader();
}

// Inicializa a compatibilidade com plugins
\WPFramework\Core\PluginCompatibility::init();

// Adiciona suporte a recursos do tema
function wpframework_setup() {
    // Adiciona suporte a título
    add_theme_support('title-tag');
    
    // Adiciona suporte a miniaturas
    add_theme_support('post-thumbnails');
    
    // Adiciona suporte a HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Registra o diretório do tema
    register_theme_directory(WPFRAMEWORK_DIR);
}
add_action('after_setup_theme', 'wpframework_setup', 1);

// Carrega as traduções do tema
function wpframework_load_theme_textdomain() {
    load_theme_textdomain('wpframework', WPFRAMEWORK_DIR . '/languages');
}
add_action('init', 'wpframework_load_theme_textdomain', 1);

// Inicializa o framework após o WordPress estar completamente carregado
function wpframework_init() {
\WPFramework\Core\Bootstrap::init();
}
add_action('init', 'wpframework_init', 20);
