<?php
/**
 * Classe base para middleware da API
 * 
 * @package WPFramework\Middleware
 */

namespace WPFramework\Middleware;

abstract class BaseMiddleware
{
    /**
     * Manipula a requisição
     * 
     * @param \WP_REST_Request $request Requisição REST
     * @return bool|\WP_REST_Response|\WP_Error
     */
    abstract public function handle($request);
    
    /**
     * Verifica o token JWT
     * 
     * @param string $token Token JWT
     * @return bool|\WP_User
     */
    protected function validateJwtToken($token)
    {
        // Verifica se o plugin JWT Authentication está ativo
        if (!class_exists('Jwt_Auth_Public')) {
            return false;
        }
        
        try {
            // Decodifica o token
            $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;
            
            if (!$secret_key) {
                return false;
            }
            
            // Usa a biblioteca JWT para decodificar o token
            $decoded_token = \Firebase\JWT\JWT::decode(
                $token,
                $secret_key,
                ['HS256']
            );
            
            // Verifica se o token é válido
            if (!isset($decoded_token->data->user->id)) {
                return false;
            }
            
            // Obtém o usuário
            $user = get_user_by('id', $decoded_token->data->user->id);
            
            if (!$user) {
                return false;
            }
            
            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Obtém o token JWT do cabeçalho Authorization
     * 
     * @param \WP_REST_Request $request Requisição REST
     * @return string|false
     */
    protected function getAuthToken($request)
    {
        // Obtém o cabeçalho Authorization
        $auth_header = $request->get_header('Authorization');
        
        // Verifica se o cabeçalho existe
        if (!$auth_header) {
            return false;
        }
        
        // Verifica se o cabeçalho começa com "Bearer "
        if (strpos($auth_header, 'Bearer ') !== 0) {
            return false;
        }
        
        // Extrai o token
        $token = substr($auth_header, 7);
        
        // Verifica se o token existe
        if (empty($token)) {
            return false;
        }
        
        return $token;
    }
    
    /**
     * Verifica se o usuário tem uma capacidade
     * 
     * @param \WP_User $user Usuário
     * @param string $capability Capacidade
     * @return bool
     */
    protected function userCan($user, $capability)
    {
        return user_can($user, $capability);
    }
    
    /**
     * Gera uma resposta de erro
     * 
     * @param string $message Mensagem de erro
     * @param int $status Código de status HTTP
     * @param string $code Código de erro
     * @return \WP_Error
     */
    protected function error($message, $status = 401, $code = 'unauthorized')
    {
        return new \WP_Error(
            $code,
            $message,
            ['status' => $status]
        );
    }
}
