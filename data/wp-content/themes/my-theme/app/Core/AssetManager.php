<?php
/**
 * Classe para gerenciamento de assets (CSS, JavaScript, etc.)
 * 
 * @package WPFramework\Core
 */

namespace WPFramework\Core;

class AssetManager
{
    /**
     * Instância única da classe (padrão Singleton)
     * 
     * @var AssetManager
     */
    private static $instance = null;
    
    /**
     * Array de estilos registrados
     * 
     * @var array
     */
    private $styles = [];
    
    /**
     * Array de scripts registrados
     * 
     * @var array
     */
    private $scripts = [];
    
    /**
     * Array de Web Components registrados
     * 
     * @var array
     */
    private $components = [];
    
    /**
     * Construtor privado (padrão Singleton)
     */
    private function __construct()
    {
        // Registra os hooks do WordPress
        add_action('wp_enqueue_scripts', [$this, 'enqueuePublicAssets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        
        // Registra os assets padrão do tema
        $this->registerDefaultAssets();
    }
    
    /**
     * Obtém a instância única da classe (padrão Singleton)
     * 
     * @return AssetManager
     */
    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Registra os assets padrão do tema
     */
    private function registerDefaultAssets()
    {
        // Estilos
        $this->registerStyle('theme-style', 'public/css/style.css', [], '1.0.0');
        
        // Scripts
        $this->registerScript('theme-main', 'public/js/main.js', ['jquery'], '1.0.0', true);
        
        // Polyfills
        $this->registerScript('webcomponents-polyfill', 'public/js/polyfills/webcomponents-bundle.js', [], '1.0.0', true);
        $this->registerScript('dialog-polyfill', 'public/js/polyfills/dialog-polyfill.js', [], '1.0.0', true);
        $this->registerScript('popover-polyfill', 'public/js/polyfills/popover-polyfill.js', [], '1.0.0', true);
        
        // Web Components
        $this->registerComponent('wp-card', 'app/Views/components/card.php');
        $this->registerComponent('wp-modal', 'app/Views/components/modal.php');
        $this->registerComponent('wp-popover', 'app/Views/components/popover.php');
    }
    
    /**
     * Registra um estilo CSS
     * 
     * @param string $handle Identificador único do estilo
     * @param string $src Caminho relativo ao tema ou URL completa
     * @param array $deps Array de dependências
     * @param string $version Versão do estilo
     * @param string $media Tipo de mídia
     * @return AssetManager
     */
    public function registerStyle($handle, $src, $deps = [], $version = null, $media = 'all')
    {
        $this->styles[$handle] = [
            'src' => $this->getAssetUrl($src),
            'deps' => $deps,
            'version' => $version,
            'media' => $media
        ];
        
        return $this;
    }
    
    /**
     * Registra um script JavaScript
     * 
     * @param string $handle Identificador único do script
     * @param string $src Caminho relativo ao tema ou URL completa
     * @param array $deps Array de dependências
     * @param string $version Versão do script
     * @param bool $in_footer Carregar no rodapé
     * @return AssetManager
     */
    public function registerScript($handle, $src, $deps = [], $version = null, $in_footer = false)
    {
        $this->scripts[$handle] = [
            'src' => $this->getAssetUrl($src),
            'deps' => $deps,
            'version' => $version,
            'in_footer' => $in_footer
        ];
        
        return $this;
    }
    
    /**
     * Registra um Web Component
     * 
     * @param string $name Nome do componente
     * @param string $path Caminho relativo ao tema
     * @return AssetManager
     */
    public function registerComponent($name, $path)
    {
        $this->components[$name] = [
            'path' => $path
        ];
        
        return $this;
    }
    
    /**
     * Carrega os assets públicos
     */
    public function enqueuePublicAssets()
    {
        // Carrega os estilos
        foreach ($this->styles as $handle => $style) {
            wp_enqueue_style(
                $handle,
                $style['src'],
                $style['deps'],
                $style['version'],
                $style['media']
            );
        }
        
        // Carrega os scripts
        foreach ($this->scripts as $handle => $script) {
            wp_enqueue_script(
                $handle,
                $script['src'],
                $script['deps'],
                $script['version'],
                $script['in_footer']
            );
        }
        
        // Adiciona variáveis JavaScript
        wp_localize_script('theme-main', 'wpframework', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('wpframework/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
            'isLoggedIn' => is_user_logged_in(),
            'themeUrl' => get_template_directory_uri(),
            'homeUrl' => home_url()
        ]);
    }
    
    /**
     * Carrega os assets administrativos
     */
    public function enqueueAdminAssets()
    {
        // Implementação para o admin
    }
    
    /**
     * Obtém a URL completa de um asset
     * 
     * @param string $src Caminho relativo ao tema ou URL completa
     * @return string
     */
    private function getAssetUrl($src)
    {
        // Verifica se é uma URL completa
        if (strpos($src, 'http') === 0 || strpos($src, '//') === 0) {
            return $src;
        }
        
        // Retorna a URL completa
        return get_template_directory_uri() . '/' . $src;
    }
    
    /**
     * Carrega um Web Component
     * 
     * @param string $name Nome do componente
     * @param array $data Dados para o componente
     * @return void
     */
    public function loadComponent($name, $data = [])
    {
        if (!isset($this->components[$name])) {
            return;
        }
        
        $path = get_template_directory() . '/' . $this->components[$name]['path'];
        
        if (file_exists($path)) {
            include $path;
        }
    }
}
