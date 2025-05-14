<?php
/**
 * Middleware para autenticação da API
 * 
 * Versão refatorada para melhor segurança
 * 
 * @package WPFramework\Middleware
 */

namespace WPFramework\Middleware;

class ApiAuth extends BaseMiddleware
{
    /**
     * Manipula a requisição
     * 
     * @param \WP_REST_Request $request Requisição REST
     * @return bool|\WP_REST_Response|\WP_Error
     */
    public function handle($request)
    {
        // Verifica se a autenticação é necessária para esta rota
        $route = $request->get_route();
        
        // Rotas públicas que não precisam de autenticação
        $public_routes = apply_filters('wpframework_api_public_routes', [
            '/wp-json/wpframework/v1/auth/token',
            '/wp-json/wpframework/v1/cursos',
        ]);
        
        // Se a rota for pública, permite o acesso
        if (in_array($route, $public_routes)) {
            return true;
        }
        
        // Obtém o token de autenticação
        $token = $this->getAuthToken($request);
        
        // Se não houver token, retorna erro
        if (!$token) {
            return $this->error(
                __('Token de autenticação não fornecido.', 'wpframework'),
                401,
                'jwt_auth_no_token'
            );
        }
        
        // Valida o token
        $user = $this->validateJwtToken($token);
        
        // Se o token for inválido, retorna erro
        if (!$user) {
            return $this->error(
                __('Token de autenticação inválido ou expirado.', 'wpframework'),
                401,
                'jwt_auth_invalid_token'
            );
        }
        
        // Define o usuário atual
        wp_set_current_user($user->ID);
        
        // Verifica se o usuário tem permissão para acessar a API
        if (!$this->userCan($user, 'access_wpframework_api')) {
            return $this->error(
                __('Você não tem permissão para acessar esta API.', 'wpframework'),
                403,
                'jwt_auth_insufficient_permissions'
            );
        }
        
        // Registra o acesso à API para fins de auditoria
        $this->logApiAccess($user, $request);
        
        // Permite o acesso
        return true;
    }
    
    /**
     * Registra o acesso à API
     * 
     * @param \WP_User $user Usuário
     * @param \WP_REST_Request $request Requisição REST
     * @return void
     */
    private function logApiAccess($user, $request)
    {
        // Obtém informações da requisição
        $route = $request->get_route();
        $method = $request->get_method();
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        // Registra o acesso no log
        error_log(sprintf(
            '[%s] API Access: User %s (%s) accessed %s %s from %s with %s',
            date('Y-m-d H:i:s'),
            $user->user_login,
            $user->ID,
            $method,
            $route,
            $ip,
            $user_agent
        ));
        
        // Permite que plugins e temas registrem o acesso de outras formas
        do_action('wpframework_api_access_log', [
            'user_id' => $user->ID,
            'user_login' => $user->user_login,
            'route' => $route,
            'method' => $method,
            'ip' => $ip,
            'user_agent' => $user_agent,
            'timestamp' => current_time('mysql'),
        ]);
    }
}
