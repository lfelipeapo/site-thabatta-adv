<?php
/**
 * Middleware seletivo para bloqueios de requisição.
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit;
}

class Thabatta_Request_Blocker
{
    /**
     * Registrar o middleware.
     */
    public function register()
    {
        add_action('parse_request', array($this, 'handle'), 9);
    }

    /**
     * Processar bloqueios de requisição.
     *
     * @param WP $wp Instância do WordPress.
     */
    public function handle($wp)
    {
        if (!$this->is_enabled()) {
            return;
        }

        if ($this->should_bypass()) {
            return;
        }

        $request_uri = $_SERVER['REQUEST_URI'] ?? '';

        foreach ($this->get_blocked_files() as $file) {
            if ($request_uri !== '' && strpos($request_uri, $file) !== false) {
                $this->block_request('blocked_file', array(
                    'file' => $file,
                    'uri' => $request_uri,
                ));
            }
        }

        foreach ($this->get_suspicious_strings() as $string) {
            if ($request_uri !== '' && stripos($request_uri, $string) !== false) {
                $this->block_request('suspicious_request', array(
                    'match' => $string,
                    'uri' => $request_uri,
                ));
            }
        }

        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
        if ($user_agent === '' || stripos($user_agent, 'libwww-perl') !== false) {
            $this->block_request('suspicious_user_agent', array(
                'user_agent' => $user_agent,
                'uri' => $request_uri,
            ));
        }
    }

    /**
     * Verificar se o bloqueio está habilitado.
     */
    private function is_enabled()
    {
        $environment = function_exists('thabatta_get_environment_type')
            ? thabatta_get_environment_type()
            : 'production';

        $enabled = ($environment === 'production');

        return (bool) apply_filters('thabatta_request_blocker_enabled', $enabled, $environment);
    }

    /**
     * Determinar quando não aplicar bloqueios.
     */
    private function should_bypass()
    {
        return is_admin()
            || wp_doing_ajax()
            || wp_doing_cron()
            || (defined('REST_REQUEST') && REST_REQUEST);
    }

    /**
     * Bloquear requisição e registrar log.
     */
    private function block_request($type, array $data)
    {
        do_action('thabatta_security_event', $type, $data);
        status_header(403);
        exit('Acesso negado');
    }

    /**
     * Lista de arquivos sensíveis.
     */
    private function get_blocked_files()
    {
        return array(
            'wp-config.php',
            '.htaccess',
            'readme.html',
            'license.txt',
            'error_log',
            'install.php',
            'wp-includes',
            'wp-admin/install.php',
            'wp-admin/includes',
            'wp-admin/setup-config.php',
        );
    }

    /**
     * Lista de strings suspeitas.
     */
    private function get_suspicious_strings()
    {
        return array(
            'eval(',
            'UNION+SELECT',
            'UNION SELECT',
            '<script',
            '../',
            'base64_',
            '<?php',
            'data:text',
            'alert(',
            'document.cookie',
            'onmouseover=',
            'javascript:',
            'prompt(',
            'fromCharCode',
        );
    }
}
