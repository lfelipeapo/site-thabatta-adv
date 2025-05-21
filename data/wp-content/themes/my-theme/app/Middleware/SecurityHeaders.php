<?php
/**
 * Middleware para cabeçalhos de segurança da API
 * 
 * Versão refatorada para melhor segurança
 * 
 * @package WPFramework\Middleware
 */

namespace WPFramework\Middleware;

class SecurityHeaders extends BaseMiddleware
{
    /**
     * Manipula a requisição
     * 
     * @param \WP_REST_Request $request Requisição REST
     * @return bool
     */
    public function handle($request)
    {
        // Define os cabeçalhos de segurança
        $this->setSecurityHeaders();
        
        // Permite o acesso
        return true;
    }
    
    /**
     * Define os cabeçalhos de segurança
     * 
     * @return void
     */
    private function setSecurityHeaders()
    {
        // Content-Security-Policy
        $csp = $this->getContentSecurityPolicy();
        if (!empty($csp)) {
            header('Content-Security-Policy: ' . $csp);
        }
        
        // X-Content-Type-Options
        header('X-Content-Type-Options: nosniff');
        
        // X-Frame-Options
        header('X-Frame-Options: SAMEORIGIN');
        
        // X-XSS-Protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Referrer-Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Strict-Transport-Security
        if (is_ssl()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
        
        // Permissions-Policy
        $permissions_policy = $this->getPermissionsPolicy();
        if (!empty($permissions_policy)) {
            header('Permissions-Policy: ' . $permissions_policy);
        }
        
        // Cache-Control
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        
        // Permite que plugins e temas adicionem cabeçalhos de segurança
        do_action('wpframework_security_headers');
    }
    
    /**
     * Obtém a política de segurança de conteúdo
     * 
     * @return string
     */
    private function getContentSecurityPolicy()
    {
        // Política padrão
        $policy = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://ajax.googleapis.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
            "img-src 'self' data: https:",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net",
            "connect-src 'self'",
            "media-src 'self'",
            "object-src 'none'",
            "frame-src 'self'",
            "worker-src 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "frame-ancestors 'self'",
            "upgrade-insecure-requests",
        ];
        
        // Permite que plugins e temas modifiquem a política
        $policy = apply_filters('wpframework_content_security_policy', $policy);
        
        return implode('; ', $policy);
    }
    
    /**
     * Obtém a política de permissões
     * 
     * @return string
     */
    private function getPermissionsPolicy()
    {
        // Política padrão
        $policy = [
            'accelerometer=()',
            'ambient-light-sensor=()',
            'autoplay=(self)',
            'camera=()',
            'encrypted-media=()',
            'fullscreen=(self)',
            'geolocation=(self)',
            'gyroscope=()',
            'magnetometer=()',
            'microphone=()',
            'midi=()',
            'payment=()',
            'picture-in-picture=(self)',
            'speaker=(self)',
            'usb=()',
            'vibrate=()',
            'vr=()',
        ];
        
        // Permite que plugins e temas modifiquem a política
        $policy = apply_filters('wpframework_permissions_policy', $policy);
        
        return implode(', ', $policy);
    }
}
