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

// Inicializa o framework
\WPFramework\Core\Bootstrap::init();
