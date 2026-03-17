<?php
/**
 * Plugin Name: AI Content Generator
 * Plugin URI: https://github.com/thabatta-adv/ai-content-generator
 * Description: Geração de conteúdo com inteligência artificial integrada à API Groq via Neuron PHP
 * Version: 1.0.0
 * Author: Thabatta Advocacia
 * Author URI: https://thabatta.adv.br
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-content-generator
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * @package AICG
 * @author  Thabatta Advocacia
 * @version 1.0.0
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Define constantes globais do plugin
if (!defined('AICG_VERSION')) {
    define('AICG_VERSION', '1.0.0');
}

if (!defined('AICG_PLUGIN_FILE')) {
    define('AICG_PLUGIN_FILE', __FILE__);
}

if (!defined('AICG_PLUGIN_DIR')) {
    define('AICG_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('AICG_PLUGIN_URL')) {
    define('AICG_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('AICG_MIN_WP_VERSION')) {
    define('AICG_MIN_WP_VERSION', '6.0');
}

if (!defined('AICG_MIN_PHP_VERSION')) {
    define('AICG_MIN_PHP_VERSION', '8.0');
}

if (!defined('AICG_IS_MU_PLUGIN')) {
    define(
        'AICG_IS_MU_PLUGIN',
        defined('WPMU_PLUGIN_DIR') && str_starts_with(AICG_PLUGIN_FILE, WPMU_PLUGIN_DIR)
    );
}

// Chave de criptografia derivada das constantes do WordPress
if (!defined('AICG_ENCRYPTION_KEY')) {
    $key_material = defined('AUTH_KEY') ? AUTH_KEY : 'default-auth-key';
    $salt_material = defined('SECURE_AUTH_SALT') ? SECURE_AUTH_SALT : 'default-secure-salt';
    define('AICG_ENCRYPTION_KEY', base64_encode(hash_hmac('sha256', $key_material, $salt_material, true)));
}

/**
 * Classe principal do plugin - Padrão Singleton
 *
 * @package AICG
 * @since   1.0.0
 */
class AICG_Plugin
{
    /**
     * Instância única da classe
     *
     * @var AICG_Plugin|null
     */
    private static ?AICG_Plugin $instance = null;

    /**
     * Construtor privado para prevenir instanciação direta
     */
    private function __construct()
    {
        $this->check_requirements();
        $this->init();
    }

    /**
     * Previne clonagem da instância
     */
    private function __clone() {}

    /**
     * Prevents unserialization
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }

    /**
     * Obtém a instância única do plugin
     *
     * @return AICG_Plugin
     */
    public static function get_instance(): AICG_Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Verifica requisitos mínimos do sistema
     *
     * @return void
     */
    private function check_requirements(): void
    {
        global $wp_version;

        $errors = [];

        // Verifica versão do PHP
        if (version_compare(PHP_VERSION, AICG_MIN_PHP_VERSION, '<')) {
            $errors[] = sprintf(
                /* translators: 1: Required PHP version, 2: Current PHP version */
                esc_html__('O plugin requer PHP %1$s ou superior. Você está executando PHP %2$s.', 'ai-content-generator'),
                AICG_MIN_PHP_VERSION,
                PHP_VERSION
            );
        }

        // Verifica versão do WordPress
        if (version_compare($wp_version, AICG_MIN_WP_VERSION, '<')) {
            $errors[] = sprintf(
                /* translators: 1: Required WordPress version, 2: Current WordPress version */
                esc_html__('O plugin requer WordPress %1$s ou superior. Você está executando WordPress %2$s.', 'ai-content-generator'),
                AICG_MIN_WP_VERSION,
                $wp_version
            );
        }

        // Verifica extensão libsodium (opcional mas recomendada)
        if (!extension_loaded('sodium')) {
            add_action('admin_notices', [$this, 'libsodium_notice']);
        }

        if (!empty($errors)) {
            add_action('admin_init', function () use ($errors) {
                foreach ($errors as $error) {
                    add_action('admin_notices', function () use ($error) {
                        echo '<div class="notice notice-error"><p><strong>AI Content Generator:</strong> ' . esc_html($error) . '</p></div>';
                    });
                }
                
                // Desativa o plugin
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            });
            
            add_action('admin_init', function () {
                deactivate_plugins(plugin_basename(AICG_PLUGIN_FILE));
            });
        }
    }

    /**
     * Exibe aviso sobre libsodium
     *
     * @return void
     */
    public function libsodium_notice(): void
    {
        echo '<div class="notice notice-warning"><p><strong>AI Content Generator:</strong> ' . 
             esc_html__('A extensão libsodium não está disponível. A criptografia de chaves API usará método alternativo menos seguro.', 'ai-content-generator') . 
             '</p></div>';
    }

    /**
     * Inicializa o plugin
     *
     * @return void
     */
    private function init(): void
    {
        // Carrega autoloader
        $this->load_autoloader();

        if (AICG_IS_MU_PLUGIN) {
            $this->bootstrap_mu_plugin();
        } else {
            // Registra hooks de ciclo de vida para instalação como plugin comum
            register_activation_hook(AICG_PLUGIN_FILE, [$this, 'activate']);
            register_deactivation_hook(AICG_PLUGIN_FILE, [$this, 'deactivate']);
            register_uninstall_hook(AICG_PLUGIN_FILE, [self::class, 'uninstall']);
        }

        if (did_action('plugins_loaded')) {
            $this->load_plugin();
        } else {
            add_action('plugins_loaded', [$this, 'load_plugin']);
        }
    }

    /**
     * Carrega o autoloader de classes
     *
     * @return void
     */
    private function load_autoloader(): void
    {
        require_once AICG_PLUGIN_DIR . 'includes/Core/Autoloader.php';
        \AICG\Core\Autoloader::register();
    }

    /**
     * Carrega e inicializa os componentes do plugin
     *
     * @return void
     */
    public function load_plugin(): void
    {
        // Carrega textdomain para internacionalização
        if (AICG_IS_MU_PLUGIN) {
            load_muplugin_textdomain(
                'ai-content-generator',
                dirname(plugin_basename(AICG_PLUGIN_FILE)) . '/languages/'
            );
        } else {
            load_plugin_textdomain(
                'ai-content-generator',
                false,
                dirname(plugin_basename(AICG_PLUGIN_FILE)) . '/languages/'
            );
        }

        // Inicializa componentes principais em ordem de dependência
        new \AICG\Core\Plugin();
        new \AICG\Admin\Menu();
        new \AICG\Admin\Assets();
        new \AICG\REST\Routes();
        new \AICG\Content\Scheduler();
        
        // Inicializa integração com SEO se necessário
        new \AICG\SEO\SEOIntegration();
    }

    /**
     * Ativação do plugin
     *
     * @return void
     */
    public function activate(): void
    {
        \AICG\Core\Activator::activate();
    }

    /**
     * Desativação do plugin
     *
     * @return void
     */
    public function deactivate(): void
    {
        \AICG\Core\Deactivator::deactivate();
    }

    /**
     * Desinstalação do plugin
     *
     * @return void
     */
    public static function uninstall(): void
    {
        \AICG\Core\Deactivator::uninstall();
    }

    /**
     * Inicializa recursos que normalmente dependeriam da ativação
     * quando o plugin roda como MU plugin.
     *
     * @return void
     */
    private function bootstrap_mu_plugin(): void
    {
        $needs_bootstrap = get_option('aicg_version') === false
            || \AICG\Core\Activator::needs_db_update();

        if ($needs_bootstrap) {
            \AICG\Core\Activator::activate();
        }
    }
}

// Inicializa o plugin
add_action('plugins_loaded', function () {
    AICG_Plugin::get_instance();
});
