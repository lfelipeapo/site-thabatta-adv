<?php
/**
 * Middleware para CORS da API
 * 
 * Versão refatorada para melhor segurança
 * 
 * @package WPFramework\Middleware
 */

namespace WPFramework\Middleware;

class ApiCors extends BaseMiddleware
{
    /**
     * Manipula a requisição
     * 
     * @param \WP_REST_Request $request Requisição REST
     * @return bool|\WP_REST_Response|\WP_Error
     */
    public function handle($request)
    {
        // Obtém os domínios permitidos
        $allowed_origins = $this->getAllowedOrigins();
        
        // Obtém a origem da requisição
        $origin = $request->get_header('origin');
        
        // Se não houver origem, permite o acesso
        if (!$origin) {
            return true;
        }
        
        // Verifica se a origem está na lista de permitidos
        $is_allowed = false;
        
        foreach ($allowed_origins as $allowed_origin) {
            // Verifica se é um wildcard
            if ($allowed_origin === '*') {
                $is_allowed = true;
                break;
            }
            
            // Verifica se é um domínio específico
            if ($allowed_origin === $origin) {
                $is_allowed = true;
                break;
            }
            
            // Verifica se é um padrão com wildcard
            if (strpos($allowed_origin, '*') !== false) {
                $pattern = str_replace('*', '.*', $allowed_origin);
                $pattern = '/^' . preg_quote($pattern, '/') . '$/';
                
                if (preg_match($pattern, $origin)) {
                    $is_allowed = true;
                    break;
                }
            }
        }
        
        // Se a origem não estiver na lista de permitidos, retorna erro
        if (!$is_allowed) {
            return $this->error(
                __('Origem não permitida.', 'wpframework'),
                403,
                'cors_origin_not_allowed'
            );
        }
        
        // Define os cabeçalhos CORS
        $this->setCorsHeaders($origin, $request->get_method());
        
        // Se for uma requisição OPTIONS (preflight), retorna uma resposta vazia
        if ($request->get_method() === 'OPTIONS') {
            return new \WP_REST_Response(null, 200);
        }
        
        // Permite o acesso
        return true;
    }
    
    /**
     * Obtém os domínios permitidos
     * 
     * @return array
     */
    private function getAllowedOrigins()
    {
        // Domínios permitidos por padrão
        $default_origins = [
            get_home_url(),
        ];
        
        // Permite que plugins e temas modifiquem os domínios permitidos
        return apply_filters('wpframework_api_allowed_origins', $default_origins);
    }
    
    /**
     * Define os cabeçalhos CORS
     * 
     * @param string $origin Origem da requisição
     * @param string $method Método HTTP
     * @return void
     */
    private function setCorsHeaders($origin, $method)
    {
        // Define o cabeçalho Access-Control-Allow-Origin
        header('Access-Control-Allow-Origin: ' . esc_url_raw($origin));
        
        // Define o cabeçalho Access-Control-Allow-Methods
        $allowed_methods = 'GET, POST, PUT, DELETE, OPTIONS';
        header('Access-Control-Allow-Methods: ' . $allowed_methods);
        
        // Define o cabeçalho Access-Control-Allow-Headers
        $allowed_headers = 'Authorization, Content-Type, X-WP-Nonce';
        header('Access-Control-Allow-Headers: ' . $allowed_headers);
        
        // Define o cabeçalho Access-Control-Allow-Credentials
        header('Access-Control-Allow-Credentials: true');
        
        // Define o cabeçalho Access-Control-Max-Age
        header('Access-Control-Max-Age: 3600');
    }
}
