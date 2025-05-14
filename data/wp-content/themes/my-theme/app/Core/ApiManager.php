<?php
/**
 * Gerenciador de API REST
 * 
 * Versão refatorada para melhor segurança e integração com o WordPress
 * 
 * @package WPFramework\Core
 */

namespace WPFramework\Core;

class ApiManager
{
    /**
     * Instância única da classe (padrão Singleton)
     * 
     * @var ApiManager
     */
    private static $instance = null;
    
    /**
     * Namespace da API
     * 
     * @var string
     */
    private $namespace = 'wpframework/v1';
    
    /**
     * Middleware registrado
     * 
     * @var array
     */
    private $middleware = [];
    
    /**
     * Construtor privado (padrão Singleton)
     */
    private function __construct()
    {
        // Registra os hooks do WordPress
        add_action('rest_api_init', [$this, 'registerRoutes']);
        
        // Carrega as rotas da API
        $this->loadApiRoutes();
    }
    
    /**
     * Obtém a instância única da classe (padrão Singleton)
     * 
     * @return ApiManager
     */
    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Carrega as rotas da API
     */
    private function loadApiRoutes()
    {
        // Carrega as rotas da API
        $api_file = get_template_directory() . '/app/api.php';
        if (file_exists($api_file)) {
            require_once $api_file;
        }
        
        // Permite que plugins e temas filhos adicionem rotas
        do_action('wpframework_load_api_routes', $this);
    }
    
    /**
     * Define o namespace da API
     * 
     * @param string $namespace Namespace da API
     * @return ApiManager
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }
    
    /**
     * Obtém o namespace da API
     * 
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
    
    /**
     * Registra middleware para a API
     * 
     * @param string $name Nome do middleware
     * @param callable|string $callback Callback do middleware
     * @return ApiManager
     */
    public function middleware($name, $callback)
    {
        $this->middleware[$name] = $callback;
        return $this;
    }
    
    /**
     * Registra uma rota GET
     * 
     * @param string $route Rota da API
     * @param callable|string $callback Callback da rota
     * @param array $options Opções da rota
     * @return ApiManager
     */
    public function get($route, $callback, $options = [])
    {
        return $this->addRoute('GET', $route, $callback, $options);
    }
    
    /**
     * Registra uma rota POST
     * 
     * @param string $route Rota da API
     * @param callable|string $callback Callback da rota
     * @param array $options Opções da rota
     * @return ApiManager
     */
    public function post($route, $callback, $options = [])
    {
        return $this->addRoute('POST', $route, $callback, $options);
    }
    
    /**
     * Registra uma rota PUT
     * 
     * @param string $route Rota da API
     * @param callable|string $callback Callback da rota
     * @param array $options Opções da rota
     * @return ApiManager
     */
    public function put($route, $callback, $options = [])
    {
        return $this->addRoute('PUT', $route, $callback, $options);
    }
    
    /**
     * Registra uma rota DELETE
     * 
     * @param string $route Rota da API
     * @param callable|string $callback Callback da rota
     * @param array $options Opções da rota
     * @return ApiManager
     */
    public function delete($route, $callback, $options = [])
    {
        return $this->addRoute('DELETE', $route, $callback, $options);
    }
    
    /**
     * Adiciona uma rota
     * 
     * @param string $method Método HTTP
     * @param string $route Rota da API
     * @param callable|string $callback Callback da rota
     * @param array $options Opções da rota
     * @return ApiManager
     */
    private function addRoute($method, $route, $callback, $options = [])
    {
        // Armazena a rota para registro posterior
        add_action('rest_api_init', function() use ($method, $route, $callback, $options) {
            // Registra a rota
            register_rest_route($this->namespace, $route, [
                'methods' => $method,
                'callback' => [$this, 'handleRequest'],
                'permission_callback' => [$this, 'checkPermissions'],
                'args' => $this->getRouteArgs($options),
                'wpframework_callback' => $callback,
                'wpframework_middleware' => isset($options['middleware']) ? $options['middleware'] : [],
                'wpframework_permissions' => isset($options['permissions']) ? $options['permissions'] : [],
            ]);
        });
        
        return $this;
    }
    
    /**
     * Obtém os argumentos da rota
     * 
     * @param array $options Opções da rota
     * @return array
     */
    private function getRouteArgs($options)
    {
        // Argumentos padrão
        $args = [];
        
        // Adiciona os argumentos da rota
        if (isset($options['args']) && is_array($options['args'])) {
            foreach ($options['args'] as $name => $arg) {
                $args[$name] = $arg;
            }
        }
        
        return $args;
    }
    
    /**
     * Verifica as permissões da rota
     * 
     * @param \WP_REST_Request $request Requisição REST
     * @return bool|WP_Error
     */
    public function checkPermissions($request)
    {
        // Obtém as permissões da rota
        $permissions = $request->get_attributes()['wpframework_permissions'] ?? [];
        
        // Se não houver permissões, permite o acesso
        if (empty($permissions)) {
            return true;
        }
        
        // Verifica cada permissão
        foreach ($permissions as $permission) {
            // Se for uma string, verifica a capacidade
            if (is_string($permission)) {
                if (!current_user_can($permission)) {
                    return new \WP_Error(
                        'rest_forbidden',
                        __('Você não tem permissão para acessar este endpoint.', 'wpframework'),
                        ['status' => 403]
                    );
                }
            }
            // Se for um callback, executa-o
            elseif (is_callable($permission)) {
                $result = call_user_func($permission, $request);
                if ($result === false) {
                    return new \WP_Error(
                        'rest_forbidden',
                        __('Você não tem permissão para acessar este endpoint.', 'wpframework'),
                        ['status' => 403]
                    );
                }
            }
        }
        
        return true;
    }
    
    /**
     * Manipula a requisição da API
     * 
     * @param \WP_REST_Request $request Requisição REST
     * @return mixed
     */
    public function handleRequest($request)
    {
        // Obtém o callback da rota
        $callback = $request->get_attributes()['wpframework_callback'] ?? null;
        
        // Obtém o middleware da rota
        $middleware = $request->get_attributes()['wpframework_middleware'] ?? [];
        
        // Executa o middleware
        $continue = $this->runMiddleware($middleware, $request);
        
        // Se o middleware retornar false, interrompe a execução
        if ($continue === false) {
            return new \WP_Error(
                'rest_forbidden',
                __('Acesso negado pelo middleware.', 'wpframework'),
                ['status' => 403]
            );
        }
        
        // Se o middleware retornar uma resposta, retorna-a
        if ($continue instanceof \WP_REST_Response || $continue instanceof \WP_Error) {
            return $continue;
        }
        
        // Executa o callback
        if (is_callable($callback)) {
            // Callback é uma função anônima
            return call_user_func($callback, $request);
        } elseif (is_string($callback) && strpos($callback, '@') !== false) {
            // Callback é uma string no formato "Controller@method"
            list($controller, $method) = explode('@', $callback);
            
            // Adiciona o namespace se não existir
            if (strpos($controller, '\\') === false) {
                $controller = '\\WPFramework\\Api\\' . $controller;
            }
            
            // Verifica se o controller existe
            if (class_exists($controller)) {
                // Cria uma instância do controller
                $instance = new $controller();
                
                // Verifica se o método existe
                if (method_exists($instance, $method)) {
                    // Executa o método
                    return call_user_func_array([$instance, $method], [$request]);
                }
            }
        }
        
        // Callback inválido
        return new \WP_Error(
            'rest_invalid_handler',
            __('O handler para a rota é inválido.', 'wpframework'),
            ['status' => 500]
        );
    }
    
    /**
     * Executa o middleware
     * 
     * @param array $middleware Middleware a ser executado
     * @param \WP_REST_Request $request Requisição REST
     * @return mixed
     */
    private function runMiddleware($middleware, $request)
    {
        // Se não houver middleware, continua a execução
        if (empty($middleware)) {
            return true;
        }
        
        // Executa cada middleware
        foreach ($middleware as $name) {
            // Verifica se o middleware existe
            if (!isset($this->middleware[$name])) {
                continue;
            }
            
            // Obtém o callback do middleware
            $callback = $this->middleware[$name];
            
            // Executa o callback
            if (is_callable($callback)) {
                // Callback é uma função anônima
                $result = call_user_func($callback, $request);
                
                // Se o middleware retornar false, interrompe a execução
                if ($result === false) {
                    return false;
                }
                
                // Se o middleware retornar uma resposta, retorna-a
                if ($result instanceof \WP_REST_Response || $result instanceof \WP_Error) {
                    return $result;
                }
            } elseif (is_string($callback) && class_exists($callback)) {
                // Callback é uma classe
                $instance = new $callback();
                
                // Verifica se o método handle existe
                if (method_exists($instance, 'handle')) {
                    // Executa o método
                    $result = call_user_func_array([$instance, 'handle'], [$request]);
                    
                    // Se o middleware retornar false, interrompe a execução
                    if ($result === false) {
                        return false;
                    }
                    
                    // Se o middleware retornar uma resposta, retorna-a
                    if ($result instanceof \WP_REST_Response || $result instanceof \WP_Error) {
                        return $result;
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Registra as rotas da API
     */
    public function registerRoutes()
    {
        // As rotas são registradas automaticamente através do hook rest_api_init
    }
    
    /**
     * Gera uma resposta de sucesso
     * 
     * @param mixed $data Dados da resposta
     * @param int $status Código de status HTTP
     * @return \WP_REST_Response
     */
    public static function success($data, $status = 200)
    {
        return new \WP_REST_Response([
            'success' => true,
            'data' => $data,
        ], $status);
    }
    
    /**
     * Gera uma resposta de erro
     * 
     * @param string $message Mensagem de erro
     * @param int $status Código de status HTTP
     * @param string $code Código de erro
     * @return \WP_Error
     */
    public static function error($message, $status = 400, $code = 'error')
    {
        return new \WP_Error($code, $message, ['status' => $status]);
    }
}
