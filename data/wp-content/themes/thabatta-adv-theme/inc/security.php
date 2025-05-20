<?php
/**
 * Funções de segurança para o tema Thabatta Advocacia
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Adicionar cabeçalhos de segurança
 */
function thabatta_security_headers() {
    // Proteção contra clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Proteção contra MIME sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Proteção XSS
    header('X-XSS-Protection: 1; mode=block');
    
    // Política de segurança de conteúdo (CSP)
    $csp = "default-src 'self' 'unsafe-inline' 'unsafe-eval' data: https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' *.googleapis.com *.gstatic.com *.google.com *.google-analytics.com *.googletagmanager.com *.jquery.com *.cloudflare.com cdnjs.cloudflare.com unpkg.com; style-src 'self' 'unsafe-inline' *.googleapis.com *.gstatic.com cdnjs.cloudflare.com unpkg.com; img-src 'self' data: https: *.googleapis.com *.gstatic.com *.google-analytics.com *.googletagmanager.com *.gravatar.com; font-src 'self' data: https: *.gstatic.com *.googleapis.com cdnjs.cloudflare.com; connect-src 'self' *.google-analytics.com *.googleapis.com; frame-src 'self' *.google.com *.youtube.com; object-src 'none'";
    header("Content-Security-Policy: $csp");
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Feature Policy
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
}
add_action('send_headers', 'thabatta_security_headers');

/**
 * Desabilitar a descoberta de XML-RPC
 */
function thabatta_disable_xmlrpc_pingback_ping($methods) {
    unset($methods['pingback.ping']);
    return $methods;
}
add_filter('xmlrpc_methods', 'thabatta_disable_xmlrpc_pingback_ping');

/**
 * Remover versão do WordPress do cabeçalho
 */
function thabatta_remove_wp_version() {
    return '';
}
add_filter('the_generator', 'thabatta_remove_wp_version');

/**
 * Remover versão do WordPress dos scripts e estilos
 */
function thabatta_remove_version_from_scripts($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'thabatta_remove_version_from_scripts', 9999);
add_filter('script_loader_src', 'thabatta_remove_version_from_scripts', 9999);

/**
 * Desabilitar o editor de arquivos no admin
 */
define('DISALLOW_FILE_EDIT', true);

/**
 * Limitar tentativas de login
 */
function thabatta_limit_login_attempts($user, $username, $password) {
    if (empty($username)) {
        return $user;
    }
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $login_attempts = get_transient('login_attempts_' . $ip);
    
    if ($login_attempts === false) {
        $login_attempts = 0;
    }
    
    if ($login_attempts >= 5) {
        return new WP_Error('too_many_attempts', __('<strong>ERRO</strong>: Muitas tentativas de login. Por favor, tente novamente mais tarde.', 'thabatta-adv'));
    }
    
    if ($user instanceof WP_Error) {
        $login_attempts++;
        set_transient('login_attempts_' . $ip, $login_attempts, 300); // 5 minutos
    } else {
        delete_transient('login_attempts_' . $ip);
    }
    
    return $user;
}
add_filter('authenticate', 'thabatta_limit_login_attempts', 30, 3);

/**
 * Sanitizar entradas de pesquisa
 */
function thabatta_sanitize_search_input($query_vars) {
    if (isset($query_vars['s'])) {
        $query_vars['s'] = sanitize_text_field($query_vars['s']);
    }
    return $query_vars;
}
add_filter('request', 'thabatta_sanitize_search_input');

/**
 * Sanitizar dados de formulários de comentários
 */
function thabatta_sanitize_comment_data($commentdata) {
    if (isset($commentdata['comment_content'])) {
        $commentdata['comment_content'] = sanitize_textarea_field($commentdata['comment_content']);
    }
    
    if (isset($commentdata['comment_author'])) {
        $commentdata['comment_author'] = sanitize_text_field($commentdata['comment_author']);
    }
    
    if (isset($commentdata['comment_author_email'])) {
        $commentdata['comment_author_email'] = sanitize_email($commentdata['comment_author_email']);
    }
    
    if (isset($commentdata['comment_author_url'])) {
        $commentdata['comment_author_url'] = esc_url_raw($commentdata['comment_author_url']);
    }
    
    return $commentdata;
}
add_filter('preprocess_comment', 'thabatta_sanitize_comment_data');

/**
 * Bloquear requisições suspeitas
 */
function thabatta_block_suspicious_requests() {
    // Não executar esta verificação no admin
    if (is_admin()) {
        return;
    }

    // Verificar strings suspeitas na URL
    $request_uri = $_SERVER['REQUEST_URI'];
    $suspicious_strings = array(
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
        'fromCharCode'
    );
    
    foreach ($suspicious_strings as $string) {
        if (stripos($request_uri, $string) !== false) {
            header('HTTP/1.1 403 Forbidden');
            exit('Acesso negado');
        }
    }
    
    // Verificar User-Agent vazio ou suspeito
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (empty($user_agent) || stripos($user_agent, 'libwww-perl') !== false) {
        header('HTTP/1.1 403 Forbidden');
        exit('Acesso negado');
    }
}
add_action('init', 'thabatta_block_suspicious_requests');

/**
 * Proteger contra ataques de força bruta na API REST
 */
function thabatta_limit_rest_api_requests($result, $server, $request) {
    if (!is_user_logged_in() && strpos($request->get_route(), '/wp/v2/users') !== false) {
        return new WP_Error('rest_forbidden', __('Acesso negado.', 'thabatta-adv'), array('status' => 403));
    }
    return $result;
}
add_filter('rest_pre_dispatch', 'thabatta_limit_rest_api_requests', 10, 3);

/**
 * Adicionar nonce aos formulários
 */
function thabatta_add_nonce_to_forms($content) {
    if (strpos($content, '<form') !== false) {
        $content = preg_replace('/(<form[^>]*method=[\'"]post[\'"][^>]*>)/i', '$1' . wp_nonce_field('thabatta_form_nonce', 'thabatta_nonce', false, false), $content);
    }
    return $content;
}
add_filter('the_content', 'thabatta_add_nonce_to_forms');

/**
 * Proteger contra ataques XSS em comentários
 */
function thabatta_filter_comment_content($content) {
    return wp_kses($content, array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'rel' => array(),
        ),
        'em' => array(),
        'strong' => array(),
        'p' => array(),
        'br' => array(),
    ));
}
add_filter('comment_text', 'thabatta_filter_comment_content');
add_filter('comment_excerpt', 'thabatta_filter_comment_content');

/**
 * Proteger contra ataques de injeção SQL
 */
function thabatta_check_for_sql_injection()
{
    // Não executar esta verificação ampla em páginas de admin; 
    // Confiar nas nonces e sanitização do core.
    if (is_admin()) {
        return;
    }

    $inputs = array_merge($_GET, $_POST, $_REQUEST, $_COOKIE);
    $patterns = array(
        '/(\%27)|(\')|(\-\-)|(\%23)|(#)/i', // Aspas simples, comentários SQL
        '/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(\;))/i', // Condições maliciosas (ex: OR '1'='1')
        '/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/i', // Tentativas de ' OR '
        '/((\%27)|(\'))union/i', // Ataques UNION
        '/\b(ALTER|CREATE|DELETE|DROP|EXEC|INSERT|MERGE|SELECT|UPDATE|UNION)\b/i', // Palavras-chave SQL
        '/\b(sleep|benchmark)\s*\(/i' // Funções de tempo
    );

    foreach ($inputs as $key => $value) {
        // Processar apenas strings
        if (is_string($value)) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    // Log da tentativa de ataque (opcional)
                    error_log('Tentativa de SQL Injection detectada: IP ' . $_SERVER['REMOTE_ADDR'] . ', Chave: ' . $key . ', Valor: ' . $value);
                    wp_die(__('Requisição inválida detectada. Tentativa de SQL Injection.', 'thabatta-adv'), __('Erro de Segurança', 'thabatta-adv'), array('response' => 403));
                }
            }
        } elseif (is_array($value)) {
            // Se for um array, verificar recursivamente (opcional, pode ser complexo)
            // thabatta_check_array_for_sql_injection($value, $patterns, $key);
        }
    }
}

// Função auxiliar para verificar arrays (se necessário)
/*
function thabatta_check_array_for_sql_injection($array, $patterns, $parent_key = '') {
    foreach ($array as $key => $value) {
        $current_key = $parent_key ? $parent_key . '[' . $key . ']' : $key;
        if (is_string($value)) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    error_log('Tentativa de SQL Injection detectada (array): IP ' . $_SERVER['REMOTE_ADDR'] . ', Chave: ' . $current_key . ', Valor: ' . $value);
                    wp_die(__('Requisição inválida detectada. Tentativa de SQL Injection.', 'thabatta-adv'), __('Erro de Segurança', 'thabatta-adv'), array('response' => 403));
                }
            }
        } elseif (is_array($value)) {
            thabatta_check_array_for_sql_injection($value, $patterns, $current_key);
        }
    }
}
*/
// add_action('init', 'thabatta_check_for_sql_injection', 1); // Executar cedo

/**
 * Proteger uploads de arquivos
 */
/* // TEMPORARIAMENTE COMENTADO PARA TESTE
function thabatta_secure_upload_files($file) {
    // Verificar extensão do arquivo
    $file_name = isset($file['name']) ? $file['name'] : '';
    $file_type = isset($file['type']) ? $file['type'] : '';
    
    // Lista de extensões permitidas
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'svg'); // Adicione SVG se necessário
    
    // Obter extensão do arquivo
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Verificar se a extensão é permitida
    if (!in_array($ext, $allowed_extensions)) {
        return array('error' => __('Tipo de arquivo não permitido.', 'thabatta-adv') . ' (' . $ext . ')');
    }
    
    // Verificar mime type
    $allowed_mime_types = array(
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/svg+xml', // Adicione SVG MIME type
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain'
    );
    
    if (!in_array($file_type, $allowed_mime_types)) {
        return array('error' => __('Tipo de MIME não permitido.', 'thabatta-adv') . ' (' . $file_type . ')');
    }
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'thabatta_secure_upload_files');
*/

/**
 * Bloquear acesso a arquivos sensíveis
 */
function thabatta_block_sensitive_files() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $blocked_files = array(
        'wp-config.php',
        '.htaccess',
        'readme.html',
        'license.txt',
        'error_log',
        'install.php',
        'wp-includes',
        'wp-admin/install.php',
        'wp-admin/includes',
        'wp-admin/setup-config.php'
    );
    
    foreach ($blocked_files as $file) {
        if (strpos($request_uri, $file) !== false) {
            header('HTTP/1.1 403 Forbidden');
            exit('Acesso negado');
        }
    }
}
add_action('init', 'thabatta_block_sensitive_files');

/**
 * Adicionar proteção contra CSRF
 */
/* Comentado: Redundante/Conflitante com nonces do admin
function thabatta_csrf_protection() {
    if (is_admin() && current_user_can('edit_posts')) {
        // Verificar nonce para ações de administração
        if (!empty($_POST) && !isset($_POST['_wpnonce'])) {
            wp_die(__('Verificação de segurança falhou. Por favor, tente novamente.', 'thabatta-adv'));
        }
    }
}
add_action('admin_init', 'thabatta_csrf_protection');
*/

/**
 * Adicionar proteção contra ataques de força bruta no login
 */
function thabatta_login_protection() {
    // Adicionar atraso para dificultar ataques de força bruta
    sleep(1);
}
add_action('login_init', 'thabatta_login_protection');

/**
 * Desabilitar listagem de diretórios
 */
function thabatta_disable_directory_listing() {
    // Criar ou atualizar arquivo .htaccess
    $htaccess_file = ABSPATH . '.htaccess';
    
    if (file_exists($htaccess_file) && is_writable($htaccess_file)) {
        $htaccess_content = file_get_contents($htaccess_file);
        
        if (strpos($htaccess_content, 'Options -Indexes') === false) {
            $htaccess_content .= "\n# Desabilitar listagem de diretórios\nOptions -Indexes\n";
            file_put_contents($htaccess_file, $htaccess_content);
        }
    }
}
add_action('admin_init', 'thabatta_disable_directory_listing');

/**
 * Proteger contra ataques de Cross-Site Scripting (XSS)
 */
function thabatta_protect_against_xss($content) {
    return wp_kses_post($content);
}
add_filter('the_content', 'thabatta_protect_against_xss');
add_filter('the_title', 'thabatta_protect_against_xss');
add_filter('comment_text', 'thabatta_protect_against_xss');
add_filter('widget_text_content', 'thabatta_protect_against_xss');

/**
 * Proteger contra ataques de Cross-Site Request Forgery (CSRF)
 */
/* Comentado: Redundante/Conflitante com nonces do admin
function thabatta_protect_against_csrf() {
    if (is_admin() && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('thabatta_admin_action');
    }
}
add_action('admin_init', 'thabatta_protect_against_csrf');
*/

/**
 * Adicionar proteção contra ataques de clickjacking
 */
function thabatta_protect_against_clickjacking() {
    header('X-Frame-Options: SAMEORIGIN');
}
add_action('send_headers', 'thabatta_protect_against_clickjacking');

/**
 * Proteger contra ataques de MIME sniffing
 */
function thabatta_protect_against_mime_sniffing() {
    header('X-Content-Type-Options: nosniff');
}
add_action('send_headers', 'thabatta_protect_against_mime_sniffing');

/**
 * Proteger contra ataques de Cross-Site Scripting (XSS)
 */
function thabatta_protect_against_xss_header() {
    header('X-XSS-Protection: 1; mode=block');
}
add_action('send_headers', 'thabatta_protect_against_xss_header');

/**
 * Adicionar proteção contra ataques de força bruta na API REST
 */
function thabatta_protect_rest_api() {
    if (!is_user_logged_in()) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $api_requests = get_transient('api_requests_' . $ip);
        
        if ($api_requests === false) {
            $api_requests = 1;
        } else {
            $api_requests++;
        }
        
        set_transient('api_requests_' . $ip, $api_requests, 60); // 1 minuto
        
        if ($api_requests > 60) { // Limite de 60 requisições por minuto
            return new WP_Error('too_many_requests', __('Muitas requisições. Por favor, tente novamente mais tarde.', 'thabatta-adv'), array('status' => 429));
        }
    }
    
    return null;
}
add_filter('rest_authentication_errors', 'thabatta_protect_rest_api');

/**
 * Adicionar proteção contra ataques de força bruta no login
 */
function thabatta_login_failed($username) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $failed_login_count = get_transient('failed_login_count_' . $ip);
    
    if ($failed_login_count === false) {
        $failed_login_count = 1;
    } else {
        $failed_login_count++;
    }
    
    set_transient('failed_login_count_' . $ip, $failed_login_count, 3600); // 1 hora
    
    if ($failed_login_count >= 5) {
        // Bloquear IP por 1 hora
        set_transient('ip_blocked_' . $ip, 1, 3600);
    }
}
add_action('wp_login_failed', 'thabatta_login_failed');

/**
 * Verificar se o IP está bloqueado
 */
function thabatta_check_blocked_ip() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $blocked = get_transient('ip_blocked_' . $ip);
    
    if ($blocked) {
        wp_die(__('Acesso temporariamente bloqueado devido a muitas tentativas de login falhas. Por favor, tente novamente mais tarde.', 'thabatta-adv'));
    }
}
add_action('login_init', 'thabatta_check_blocked_ip');

/**
 * Limpar transients de bloqueio após login bem-sucedido
 */
function thabatta_clear_login_transients($user_login, $user) {
    $ip = $_SERVER['REMOTE_ADDR'];
    delete_transient('failed_login_count_' . $ip);
    delete_transient('ip_blocked_' . $ip);
}
add_action('wp_login', 'thabatta_clear_login_transients', 10, 2);
