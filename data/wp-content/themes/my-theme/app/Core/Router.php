<?php
/**
 * Classe para gerenciamento de rotas
 * 
 * Versão refatorada para melhor integração com o fluxo do WordPress
 * 
 * @package WPFramework\Core
 */

namespace WPFramework\Core;

class Router
{
    /**
     * Instância única da classe (padrão Singleton)
     * 
     * @var Router
     */
    private static $instance = null;
    
    /**
     * Array de rotas registradas
     * 
     * @var array
     */
    private $routes = [];
    
    /**
     * Array de middlewares globais
     * 
     * @var array
     */
    private $middlewares = [];
    
    /**
     * Rota atual
     * 
     * @var array|null
     */
    private $current_route = null;
    
    /**
     * Parâmetros da rota atual
     * 
     * @var array
     */
    private $params = [];
    
    /**
     * Construtor privado (padrão Singleton)
     */
    private function __construct()
    {
        // Registra os hooks do WordPress
        add_action('init', [$this, 'registerRewriteRules']);
        add_action('template_redirect', [$this, 'handleRequest'], 1);
        add_filter('query_vars', [$this, 'registerQueryVars']);
        
        // Carrega as rotas
        $this->loadRoutes();
    }
    
    /**
     * Obtém a instância única da classe (padrão Singleton)
     * 
     * @return Router
     */
    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Carrega as rotas
     */
    private function loadRoutes()
    {
        // Carrega as rotas do arquivo de rotas
        $routes_file = get_template_directory() . '/app/routes.php';
        if (file_exists($routes_file)) {
            require_once $routes_file;
        }
        
        // Carrega as rotas de cursos
        $routes_cursos_file = get_template_directory() . '/app/routes-cursos.php';
        if (file_exists($routes_cursos_file)) {
            require_once $routes_cursos_file;
        }
        
        // Permite que plugins e temas filhos adicionem rotas
        do_action('wpframework_load_routes', $this);
    }
    
    /**
     * Registra uma rota GET
     * 
     * @param string $pattern Padrão da URL
     * @param string|callable $callback Callback da rota
     * @return Router
     */
    public function get($pattern, $callback)
    {
        return $this->addRoute('GET', $pattern, $callback);
    }
    
    /**
     * Registra uma rota POST
     * 
     * @param string $pattern Padrão da URL
     * @param string|callable $callback Callback da rota
     * @return Router
     */
    public function post($pattern, $callback)
    {
        return $this->addRoute('POST', $pattern, $callback);
    }
    
    /**
     * Registra uma rota PUT
     * 
     * @param string $pattern Padrão da URL
     * @param string|callable $callback Callback da rota
     * @return Router
     */
    public function put($pattern, $callback)
    {
        return $this->addRoute('PUT', $pattern, $callback);
    }
    
    /**
     * Registra uma rota DELETE
     * 
     * @param string $pattern Padrão da URL
     * @param string|callable $callback Callback da rota
     * @return Router
     */
    public function delete($pattern, $callback)
    {
        return $this->addRoute('DELETE', $pattern, $callback);
    }
    
    /**
     * Registra uma rota para qualquer método HTTP
     * 
     * @param string $pattern Padrão da URL
     * @param string|callable $callback Callback da rota
     * @return Router
     */
    public function any($pattern, $callback)
    {
        return $this->addRoute('ANY', $pattern, $callback);
    }
    
    /**
     * Adiciona uma rota
     * 
     * @param string $method Método HTTP
     * @param string $pattern Padrão da URL
     * @param string|callable $callback Callback da rota
     * @return Router
     */
    private function addRoute($method, $pattern, $callback)
    {
        // Normaliza o padrão
        $pattern = $this->normalizePattern($pattern);
        
        // Adiciona a rota
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'regex' => $this->patternToRegex($pattern),
        ];
        
        return $this;
    }
    
    /**
     * Normaliza o padrão da URL
     * 
     * @param string $pattern Padrão da URL
     * @return string
     */
    private function normalizePattern($pattern)
    {
        // Remove a barra inicial se existir
        $pattern = ltrim($pattern, '/');
        
        // Adiciona a barra final se não existir
        if (!empty($pattern) && substr($pattern, -1) !== '/') {
            $pattern .= '/';
        }
        
        return $pattern;
    }
    
    /**
     * Converte o padrão da URL em expressão regular
     * 
     * @param string $pattern Padrão da URL
     * @return string
     */
    private function patternToRegex($pattern)
    {
        // Substitui os parâmetros por expressões regulares
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        
        // Adiciona os delimitadores
        $regex = '#^' . $regex . '$#';
        
        return $regex;
    }
    
    /**
     * Registra as regras de reescrita no WordPress
     */
    public function registerRewriteRules()
    {
        // Adiciona uma regra de reescrita para cada rota
        foreach ($this->routes as $route) {
            // Converte o padrão em uma regra de reescrita
            $pattern = $route['pattern'];
            $pattern = str_replace('{', '([^/]+)/', $pattern);
            $pattern = str_replace('}', '', $pattern);
            
            // Adiciona a regra de reescrita
            add_rewrite_rule(
                '^' . $pattern . '?$',
                'index.php?wpframework_route=1',
                'top'
            );
        }
        
        // Atualiza as regras de reescrita se necessário
        $this->maybeFlushRewriteRules();
    }
    
    /**
     * Registra as variáveis de consulta no WordPress
     * 
     * @param array $vars Variáveis de consulta
     * @return array
     */
    public function registerQueryVars($vars)
    {
        $vars[] = 'wpframework_route';
        return $vars;
    }
    
    /**
     * Atualiza as regras de reescrita se necessário
     */
    private function maybeFlushRewriteRules()
    {
        // Verifica se as regras de reescrita precisam ser atualizadas
        $flush_rules = get_option('wpframework_flush_rewrite_rules', false);
        
        if ($flush_rules) {
            flush_rewrite_rules();
            update_option('wpframework_flush_rewrite_rules', false);
        }
    }
    
    /**
     * Manipula a requisição atual
     */
    public function handleRequest()
    {
        // Verifica se é uma rota do framework
        if (!get_query_var('wpframework_route')) {
            return;
        }
        
        // Obtém a URL atual
        $url = $this->getCurrentUrl();
        
        // Obtém o método HTTP
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Procura uma rota correspondente
        $route = $this->findRoute($url, $method);
        
        // Se encontrou uma rota, executa o callback
        if ($route) {
            $this->current_route = $route;
            
            // Verifica o nonce para métodos não seguros
            if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
                $this->verifyNonce();
            }
            
            // Executa os middlewares
            if (!$this->executeMiddlewares()) {
                return;
            }
            
            // Executa o callback
            $this->executeRouteCallback();
            
            // Finaliza a execução
            exit;
        }
    }
    
    /**
     * Verifica o nonce para métodos não seguros
     */
    private function verifyNonce()
    {
        // Obtém o nonce
        $nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '';
        
        // Verifica o nonce
        if (!wp_verify_nonce($nonce, 'wpframework_route')) {
            wp_die(__('Erro de segurança. Por favor, tente novamente.', 'wpframework'), __('Erro de segurança', 'wpframework'), [
                'response' => 403,
                'back_link' => true,
            ]);
        }
    }
    
    /**
     * Obtém a URL atual
     * 
     * @return string
     */
    private function getCurrentUrl()
    {
        // Obtém a URL atual
        $url = trim(esc_url_raw(add_query_arg([])), '/');
        
        // Remove o domínio e o caminho base
        $home_url = trim(home_url(), '/');
        $url = str_replace($home_url, '', $url);
        
        // Remove a query string
        $url = strtok($url, '?');
        
        // Normaliza a URL
        $url = trim($url, '/');
        
        // Adiciona a barra final
        if (!empty($url)) {
            $url .= '/';
        }
        
        return $url;
    }
    
    /**
     * Procura uma rota correspondente
     * 
     * @param string $url URL atual
     * @param string $method Método HTTP
     * @return array|null
     */
    private function findRoute($url, $method)
    {
        // Procura uma rota correspondente
        foreach ($this->routes as $route) {
            // Verifica se o método corresponde
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }
            
            // Verifica se a URL corresponde
            if (preg_match($route['regex'], $url, $matches)) {
                // Extrai os parâmetros
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = sanitize_text_field($value);
                    }
                }
                
                // Armazena os parâmetros
                $this->params = $params;
                
                return $route;
            }
        }
        
        return null;
    }
    
    /**
     * Executa o callback da rota
     */
    private function executeRouteCallback()
    {
        // Obtém o callback
        $callback = $this->current_route['callback'];
        
        // Executa o callback
        if (is_callable($callback)) {
            // Callback é uma função anônima
            $content = call_user_func_array($callback, [$this->params]);
            $this->renderContent($content);
        } elseif (is_string($callback) && strpos($callback, '@') !== false) {
            // Callback é uma string no formato "Controller@method"
            list($controller, $method) = explode('@', $callback);
            
            // Adiciona o namespace se não existir
            if (strpos($controller, '\\') === false) {
                $controller = '\\WPFramework\\Controllers\\' . $controller;
            }
            
            // Verifica se o controller existe
            if (class_exists($controller)) {
                // Cria uma instância do controller
                $instance = new $controller();
                
                // Verifica se o método existe
                if (method_exists($instance, $method)) {
                    // Executa o método
                    $content = call_user_func_array([$instance, $method], [$this->params]);
                    $this->renderContent($content);
                }
            }
        }
    }
    
    /**
     * Renderiza o conteúdo
     * 
     * @param mixed $content Conteúdo a ser renderizado
     */
    private function renderContent($content)
    {
        // Se o conteúdo for uma string, renderiza diretamente
        if (is_string($content)) {
            // Integra o conteúdo ao template do WordPress
            add_filter('template_include', function($template) use ($content) {
                // Carrega o header
                get_header();
                
                // Exibe o conteúdo
                echo $content;
                
                // Carrega o footer
                get_footer();
                
                // Retorna um template vazio para evitar que o WordPress carregue o template padrão
                return get_template_directory() . '/app/Views/empty.php';
            });
        }
    }
    
    /**
     * Obtém os parâmetros da rota atual
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Obtém um parâmetro específico da rota atual
     * 
     * @param string $name Nome do parâmetro
     * @param mixed $default Valor padrão
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name] : $default;
    }
    
    /**
     * Gera uma URL para uma rota
     * 
     * @param string $pattern Padrão da URL
     * @param array $params Parâmetros da URL
     * @return string
     */
    public function url($pattern, $params = [])
    {
        // Normaliza o padrão
        $pattern = $this->normalizePattern($pattern);
        
        // Substitui os parâmetros na URL
        foreach ($params as $key => $value) {
            $pattern = str_replace('{' . $key . '}', $value, $pattern);
        }
        
        // Retorna a URL completa
        return home_url($pattern);
    }
    
    /**
     * Gera um campo nonce para uma rota
     * 
     * @return string
     */
    public function getNonceField()
    {
        return wp_nonce_field('wpframework_route', '_wpnonce', true, false);
    }
    
    /**
     * Registra middlewares globais
     * 
     * @param array $middlewares Array de classes de middleware
     * @return Router
     */
    public function middleware(array $middlewares)
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }
    
    /**
     * Executa os middlewares registrados
     * 
     * @return bool
     */
    private function executeMiddlewares()
    {
        foreach ($this->middlewares as $middleware) {
            if (class_exists($middleware)) {
                $instance = new $middleware();
                if (method_exists($instance, 'handle')) {
                    if (!$instance->handle()) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
