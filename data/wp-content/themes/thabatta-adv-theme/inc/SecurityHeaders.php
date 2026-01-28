<?php
/**
 * Handler único para cabeçalhos de segurança.
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit;
}

class Thabatta_Security_Headers
{
    /**
     * Registrar o handler.
     */
    public function register()
    {
        add_action('send_headers', array($this, 'send'));
    }

    /**
     * Enviar cabeçalhos de segurança.
     */
    public function send()
    {
        if (headers_sent()) {
            return;
        }

        if (!$this->is_enabled()) {
            return;
        }

        $environment = function_exists('thabatta_get_environment_type')
            ? thabatta_get_environment_type()
            : 'production';

        $is_production = ($environment === 'production');

        $this->set_header('X-Frame-Options', 'SAMEORIGIN');
        $this->set_header('X-Content-Type-Options', 'nosniff');
        $this->set_header('X-XSS-Protection', '1; mode=block');
        $this->set_header('Referrer-Policy', 'strict-origin-when-cross-origin');

        if ($is_production && is_ssl()) {
            $this->set_header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        $csp = $this->get_content_security_policy($is_production);
        if (!empty($csp)) {
            $header_name = $is_production ? 'Content-Security-Policy' : 'Content-Security-Policy-Report-Only';
            $this->set_header($header_name, $csp);
        }

        $permissions_policy = $this->get_permissions_policy($is_production);
        if (!empty($permissions_policy)) {
            $this->set_header('Permissions-Policy', $permissions_policy);
        }
    }

    /**
     * Verificar se os headers estão habilitados.
     */
    private function is_enabled()
    {
        $settings = get_option('thabatta_security_settings', array(
            'enable_security_headers' => 1,
        ));

        $enabled = !empty($settings['enable_security_headers']);

        return (bool) apply_filters('thabatta_security_headers_enabled', $enabled);
    }

    /**
     * Definir header, evitando duplicação.
     */
    private function set_header($name, $value)
    {
        if (!headers_sent()) {
            header($name . ': ' . $value, true);
        }
    }

    /**
     * Política de segurança de conteúdo (CSP).
     */
    private function get_content_security_policy($is_production)
    {
        $csp = "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: https:; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://c0.wp.com https://s0.wp.com *.googleapis.com *.gstatic.com *.google.com *.google-analytics.com *.googletagmanager.com *.jquery.com *.cloudflare.com cdnjs.cloudflare.com unpkg.com; "
            . "style-src 'self' 'unsafe-inline' https://c0.wp.com https://s0.wp.com *.googleapis.com *.gstatic.com cdnjs.cloudflare.com unpkg.com; "
            . "img-src 'self' data: https: https://c0.wp.com https://s0.wp.com *.googleapis.com *.gstatic.com *.google-analytics.com *.googletagmanager.com *.gravatar.com; "
            . "font-src 'self' data: https: *.gstatic.com *.googleapis.com cdnjs.cloudflare.com; "
            . "connect-src 'self' *.google-analytics.com *.googleapis.com; "
            . "frame-src 'self' *.google.com *.youtube.com; "
            . "object-src 'none'";

        $enable_csp = $is_production
            ? true
            : (bool) apply_filters('thabatta_security_headers_enable_csp_nonprod', false);

        if (!$enable_csp) {
            return '';
        }

        return apply_filters('thabatta_security_headers_csp', $csp, $is_production);
    }

    /**
     * Política de permissões.
     */
    private function get_permissions_policy($is_production)
    {
        $policy = 'geolocation=(), microphone=(), camera=()';

        $enable_policy = $is_production
            ? true
            : (bool) apply_filters('thabatta_security_headers_enable_permissions_policy_nonprod', true);

        if (!$enable_policy) {
            return '';
        }

        return apply_filters('thabatta_security_headers_permissions_policy', $policy, $is_production);
    }
}
