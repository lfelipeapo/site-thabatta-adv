<?php
/**
 * Classe para gerenciar recursos de segurança do tema
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Classe para gerenciar recursos de segurança do tema
 */
class Thabatta_Security_Features
{
    /**
     * Inicializa a classe
     */
    public function __construct()
    {
        // Filtrar comentários para evitar spam e ataques
        add_filter('preprocess_comment', array($this, 'filter_comment'));
        add_filter('comment_text', array($this, 'sanitize_comment_text'));

        // Proteger contra ataques XSS em pesquisas
        add_filter('get_search_query', array($this, 'sanitize_search_query'));
        add_action('parse_query', array($this, 'sanitize_search_parameters'));

        // Proteger contra ataques de força bruta
        add_filter('authenticate', array($this, 'check_login_attempts'), 30, 3);
        add_action('wp_login_failed', array($this, 'log_failed_login'));

        // Proteger contra ataques de injeção SQL
        add_filter('query', array($this, 'check_query_for_sql_injection'));

        // Proteger contra ataques CSRF
        add_action('init', array($this, 'start_session'));
        add_action('wp_logout', array($this, 'end_session'));
        add_action('wp_login', array($this, 'end_session'));

        // Adicionar cabeçalhos de segurança
        add_action('send_headers', array($this, 'add_security_headers'));

        // Desabilitar listagem de diretórios
        add_action('init', array($this, 'disable_directory_listing'));

        // Proteger arquivos sensíveis
        add_action('init', array($this, 'protect_sensitive_files'));

        // Limitar tentativas de login
        add_filter('wp_login_errors', array($this, 'login_error_message'));

        // Ocultar versão do WordPress
        add_filter('the_generator', array($this, 'remove_version_info'));

        // Desabilitar edição de arquivos no admin
        if (!defined('DISALLOW_FILE_EDIT')) {
            define('DISALLOW_FILE_EDIT', true);
        }

        // Proteger contra ataques de XML-RPC
        add_filter('xmlrpc_enabled', '__return_false');

        // Proteger contra ataques de enumeração de usuários
        add_action('template_redirect', array($this, 'disable_author_pages'));

        // Proteger contra ataques de clickjacking
        add_action('send_headers', array($this, 'prevent_clickjacking'));
    }

    /**
     * Filtrar comentários para evitar spam e ataques
     */
    public function filter_comment($commentdata)
    {
        // Verificar se o comentário contém URLs suspeitos
        $comment_content = $commentdata['comment_content'];

        // Lista de padrões suspeitos
        $suspicious_patterns = array(
            // URLs suspeitos
            '/(https?:\/\/|www\.)[^\s\)]+\.(ru|cn|tk|top|xyz|pw|loan|online|win|review|stream|date|click|country|racing|faith|science|cricket|space|men|gq|link|work|ninja|bid|party|trade|webcam|science|accountant|download|xin|ren|vip|party|date|wang|win|stream|gdn|mom|racing|download|racing|review|party|click|loan|top|science|cricket|date|faith|review|accountants)/i',
            // Scripts maliciosos
            '/<script.*?>.*?<\/script>/is',
            // Iframes maliciosos
            '/<iframe.*?>.*?<\/iframe>/is',
            // Tags de estilo maliciosas
            '/<style.*?>.*?<\/style>/is',
            // Atributos on* (onclick, onload, etc.)
            '/\s+on\w+\s*=/is',
            // Atributos de dados suspeitos
            '/\s+data-\w+\s*=/is',
            // Comentários HTML
            '/<!--.*?-->/s',
            // Expressões de CSS maliciosas
            '/expression\s*\(.*?\)/is',
            // Comportamento de CSS malicioso
            '/behavior\s*:.*?/is',
            // Muitos links
            '/(http|https|ftp|ftps):\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/',
        );

        // Verificar cada padrão
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match_all($pattern, $comment_content, $matches) > 3) {
                wp_die(__('Comentário bloqueado por motivos de segurança. Por favor, remova links e tente novamente.', 'thabatta-adv'), __('Comentário Bloqueado', 'thabatta-adv'), array('response' => 403));
            }
        }

        // Verificar se o comentário contém muitos links (possível spam)
        $num_links = substr_count(strtolower($comment_content), 'http');
        if ($num_links > 5) {
            wp_die(__('Comentário bloqueado por motivos de segurança. Por favor, remova alguns links e tente novamente.', 'thabatta-adv'), __('Comentário Bloqueado', 'thabatta-adv'), array('response' => 403));
        }

        // Verificar se o comentário contém palavras suspeitas
        $suspicious_words = array(
            'viagra', 'cialis', 'casino', 'poker', 'porn', 'sex', 'loan', 'dating', 'free', 'cheap',
            'weight loss', 'diet', 'pharmacy', 'medication', 'drugs', 'pills', 'buy now', 'discount',
            'rolex', 'watches', 'replica', 'luxury', 'mortgage', 'credit', 'insurance', 'payday',
            'bitcoin', 'crypto', 'investment', 'earn money', 'make money', 'work from home', 'income',
        );

        $word_count = 0;
        foreach ($suspicious_words as $word) {
            if (stripos($comment_content, $word) !== false) {
                $word_count++;
            }
        }

        if ($word_count >= 3) {
            wp_die(__('Comentário bloqueado por motivos de segurança. Por favor, revise o conteúdo e tente novamente.', 'thabatta-adv'), __('Comentário Bloqueado', 'thabatta-adv'), array('response' => 403));
        }

        return $commentdata;
    }

    /**
     * Sanitizar texto do comentário
     */
    public function sanitize_comment_text($comment_text)
    {
        // Remover scripts e tags HTML potencialmente perigosas
        $comment_text = wp_kses($comment_text, array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'rel' => array(),
            ),
            'b' => array(),
            'strong' => array(),
            'em' => array(),
            'i' => array(),
            'p' => array(),
            'br' => array(),
        ));

        return $comment_text;
    }

    /**
     * Sanitizar consulta de pesquisa
     */
    public function sanitize_search_query($query)
    {
        // Remover caracteres especiais e limitar o tamanho
        $query = sanitize_text_field($query);
        $query = substr($query, 0, 100); // Limitar a 100 caracteres

        // Remover caracteres potencialmente perigosos
        $query = str_replace(array('<', '>', '"', "'", '\\', '/', '%', '=', '*', '&', ';'), '', $query);

        return $query;
    }

    /**
     * Sanitizar parâmetros de pesquisa
     */
    public function sanitize_search_parameters($query)
    {
        if ($query->is_search() && isset($_GET['s'])) {
            $_GET['s'] = $this->sanitize_search_query($_GET['s']);
        }
    }

    /**
     * Verificar tentativas de login
     */
    public function check_login_attempts($user, $username, $password)
    {
        if (empty($username) || empty($password)) {
            return $user;
        }

        // Obter endereço IP
        $ip = $_SERVER['REMOTE_ADDR'];

        // Verificar se o IP está bloqueado
        $blocked_ips = get_option('thabatta_blocked_ips', array());

        if (isset($blocked_ips[$ip]) && $blocked_ips[$ip]['expires'] > time()) {
            // IP bloqueado, retornar erro
            return new WP_Error('ip_blocked', __('<strong>ERRO</strong>: Muitas tentativas de login. Por favor, tente novamente mais tarde.', 'thabatta-adv'));
        }

        return $user;
    }

    /**
     * Registrar falha de login
     */
    public function log_failed_login($username)
    {
        // Obter endereço IP
        $ip = $_SERVER['REMOTE_ADDR'];

        // Obter tentativas de login
        $login_attempts = get_option('thabatta_login_attempts', array());

        // Inicializar array para este IP se não existir
        if (!isset($login_attempts[$ip])) {
            $login_attempts[$ip] = array(
                'count' => 0,
                'last_attempt' => 0,
            );
        }

        // Incrementar contador e atualizar timestamp
        $login_attempts[$ip]['count']++;
        $login_attempts[$ip]['last_attempt'] = time();

        // Salvar tentativas de login
        update_option('thabatta_login_attempts', $login_attempts);

        // Verificar se deve bloquear o IP
        if ($login_attempts[$ip]['count'] >= 5) {
            $this->block_ip($ip);
        }
    }

    /**
     * Bloquear endereço IP
     */
    private function block_ip($ip)
    {
        // Obter IPs bloqueados
        $blocked_ips = get_option('thabatta_blocked_ips', array());

        // Adicionar IP à lista de bloqueados
        $blocked_ips[$ip] = array(
            'blocked_at' => time(),
            'expires' => time() + 3600, // Bloquear por 1 hora
        );

        // Salvar IPs bloqueados
        update_option('thabatta_blocked_ips', $blocked_ips);

        // Limpar tentativas de login para este IP
        $login_attempts = get_option('thabatta_login_attempts', array());
        unset($login_attempts[$ip]);
        update_option('thabatta_login_attempts', $login_attempts);
    }

    /**
     * Verificar consulta SQL para injeção
     */
    public function check_query_for_sql_injection($query)
    {
        // Lista de padrões suspeitos
        $suspicious_patterns = array(
            '/UNION\s+SELECT/i',
            '/SELECT\s+.*\s+FROM/i',
            '/INSERT\s+INTO/i',
            '/UPDATE\s+.*\s+SET/i',
            '/DELETE\s+FROM/i',
            '/DROP\s+TABLE/i',
            '/TRUNCATE\s+TABLE/i',
            '/ALTER\s+TABLE/i',
            '/EXEC\s+/i',
            '/EXECUTE\s+/i',
            '/DECLARE\s+/i',
            '/CAST\s+/i',
            '/CONVERT\s+/i',
            '/CONCAT\s+/i',
            '/CHAR\s+/i',
            '/WAITFOR\s+DELAY/i',
            '/BENCHMARK\s+/i',
            '/SLEEP\s*\(/i',
            '/ORDER\s+BY\s+[0-9]+/i',
            '/GROUP\s+BY\s+[0-9]+/i',
            '/HAVING\s+[0-9]+/i',
            '/OR\s+1\s*=\s*1/i',
            '/AND\s+1\s*=\s*1/i',
            '/OR\s+\'1\'\s*=\s*\'1\'/i',
            '/AND\s+\'1\'\s*=\s*\'1\'/i',
            '/OR\s+[\'"][^\']*[\'"]\s*=\s*[\'"][^\']*[\'"]/',
            '/AND\s+[\'"][^\']*[\'"]\s*=\s*[\'"][^\']*[\'"]/',
            '/--\s+/',
            '/;\s*--/',
            '/;\s*#/',
            '/\'\s*OR\s*\'1\'\s*=\s*\'1\'/i',
            '/\'\s*AND\s*\'1\'\s*=\s*\'1\'/i',
            '/\'\s*OR\s*1\s*=\s*1/i',
            '/\'\s*AND\s*1\s*=\s*1/i',
        );

        // Verificar cada padrão
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $query)) {
                // Registrar tentativa de injeção SQL
                $this->log_security_event('sql_injection', array(
                    'query' => $query,
                    'pattern' => $pattern,
                ));

                // Retornar consulta vazia para evitar a injeção
                return '';
            }
        }

        return $query;
    }

    /**
     * Iniciar sessão para proteção CSRF
     */
    public function start_session()
    {
        if (!session_id()) {
            session_start();
        }

        // Gerar token CSRF se não existir
        if (!isset($_SESSION['thabatta_csrf_token'])) {
            $_SESSION['thabatta_csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Encerrar sessão
     */
    public function end_session()
    {
        if (session_id()) {
            session_destroy();
        }
    }

    /**
     * Obter token CSRF
     */
    public function get_csrf_token()
    {
        if (isset($_SESSION['thabatta_csrf_token'])) {
            return $_SESSION['thabatta_csrf_token'];
        }

        return '';
    }

    /**
     * Verificar token CSRF
     */
    public function verify_csrf_token($token)
    {
        if (isset($_SESSION['thabatta_csrf_token']) && $token === $_SESSION['thabatta_csrf_token']) {
            return true;
        }

        return false;
    }

    /**
     * Adicionar cabeçalhos de segurança
     */
    public function add_security_headers()
    {
        // Proteção contra clickjacking
        header('X-Frame-Options: SAMEORIGIN');

        // Proteção contra MIME sniffing
        header('X-Content-Type-Options: nosniff');

        // Proteção XSS
        header('X-XSS-Protection: 1; mode=block');

        // Política de segurança de conteúdo (CSP)
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' *.googleapis.com *.gstatic.com *.google.com *.google-analytics.com *.googletagmanager.com *.jquery.com *.cloudflare.com cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' *.googleapis.com *.gstatic.com cdnjs.cloudflare.com; img-src 'self' data: *.googleapis.com *.gstatic.com *.google-analytics.com *.googletagmanager.com *.gravatar.com; font-src 'self' data: *.gstatic.com *.googleapis.com cdnjs.cloudflare.com; connect-src 'self' *.google-analytics.com *.googleapis.com; frame-src 'self' *.google.com *.youtube.com; object-src 'none'";
        header("Content-Security-Policy: $csp");

        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Feature Policy
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }

    /**
     * Desabilitar listagem de diretórios
     */
    public function disable_directory_listing()
    {
        // Verificar se o arquivo .htaccess existe
        $htaccess_file = ABSPATH . '.htaccess';

        if (file_exists($htaccess_file) && is_writable($htaccess_file)) {
            $htaccess_content = file_get_contents($htaccess_file);

            // Verificar se já existe a regra
            if (strpos($htaccess_content, 'Options -Indexes') === false) {
                // Adicionar regra para desabilitar listagem de diretórios
                $htaccess_content .= "\n# Desabilitar listagem de diretórios\nOptions -Indexes\n";

                // Salvar arquivo
                file_put_contents($htaccess_file, $htaccess_content);
            }
        }
    }

    /**
     * Proteger arquivos sensíveis
     */
    public function protect_sensitive_files()
    {
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

    /**
     * Personalizar mensagem de erro de login
     */
    public function login_error_message($errors)
    {
        // Remover mensagens de erro específicas
        $error_codes = $errors->get_error_codes();

        foreach ($error_codes as $code) {
            if ($code === 'invalid_username' || $code === 'incorrect_password') {
                $errors->remove($code);
                $errors->add('login_error', __('<strong>ERRO</strong>: Nome de usuário ou senha incorretos.', 'thabatta-adv'));
                break;
            }
        }

        return $errors;
    }

    /**
     * Remover informações de versão
     */
    public function remove_version_info()
    {
        return '';
    }

    /**
     * Desabilitar páginas de autor para evitar enumeração de usuários
     */
    public function disable_author_pages()
    {
        global $wp_query;

        if (is_author()) {
            // Redirecionar para a página inicial
            wp_redirect(home_url(), 301);
            exit;
        }
    }

    /**
     * Prevenir clickjacking
     */
    public function prevent_clickjacking()
    {
        header('X-Frame-Options: SAMEORIGIN');
    }

    /**
     * Registrar evento de segurança
     */
    private function log_security_event($type, $data = array())
    {
        // Obter eventos de segurança
        $security_events = get_option('thabatta_security_events', array());

        // Adicionar novo evento
        $security_events[] = array(
            'type' => $type,
            'data' => $data,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'timestamp' => time(),
        );

        // Limitar a 100 eventos
        if (count($security_events) > 100) {
            $security_events = array_slice($security_events, -100);
        }

        // Salvar eventos
        update_option('thabatta_security_events', $security_events);
    }

    /**
     * Limpar eventos de segurança antigos
     */
    public function clean_old_security_events()
    {
        // Obter eventos de segurança
        $security_events = get_option('thabatta_security_events', array());

        // Remover eventos com mais de 30 dias
        $thirty_days_ago = time() - (30 * 24 * 60 * 60);

        foreach ($security_events as $key => $event) {
            if ($event['timestamp'] < $thirty_days_ago) {
                unset($security_events[$key]);
            }
        }

        // Reindexar array
        $security_events = array_values($security_events);

        // Salvar eventos
        update_option('thabatta_security_events', $security_events);
    }

    /**
     * Obter eventos de segurança
     */
    public function get_security_events($limit = 50)
    {
        // Obter eventos de segurança
        $security_events = get_option('thabatta_security_events', array());

        // Ordenar por timestamp (mais recentes primeiro)
        usort($security_events, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        // Limitar número de eventos
        if (count($security_events) > $limit) {
            $security_events = array_slice($security_events, 0, $limit);
        }

        return $security_events;
    }

    /**
     * Verificar se há ataques em andamento
     */
    public function check_for_ongoing_attacks()
    {
        // Obter eventos de segurança
        $security_events = get_option('thabatta_security_events', array());

        // Verificar eventos nas últimas 24 horas
        $twenty_four_hours_ago = time() - (24 * 60 * 60);
        $recent_events = array();

        foreach ($security_events as $event) {
            if ($event['timestamp'] >= $twenty_four_hours_ago) {
                $recent_events[] = $event;
            }
        }

        // Contar eventos por tipo
        $event_counts = array();

        foreach ($recent_events as $event) {
            if (!isset($event_counts[$event['type']])) {
                $event_counts[$event['type']] = 0;
            }

            $event_counts[$event['type']]++;
        }

        // Verificar se há muitos eventos de um tipo específico
        $attack_threshold = 10;
        $attacks = array();

        foreach ($event_counts as $type => $count) {
            if ($count >= $attack_threshold) {
                $attacks[] = array(
                    'type' => $type,
                    'count' => $count,
                );
            }
        }

        return $attacks;
    }

    /**
     * Adicionar widget de dashboard para eventos de segurança
     */
    public function add_security_dashboard_widget()
    {
        wp_add_dashboard_widget(
            'thabatta_security_dashboard_widget',
            __('Eventos de Segurança', 'thabatta-adv'),
            array($this, 'render_security_dashboard_widget')
        );
    }

    /**
     * Renderizar widget de dashboard para eventos de segurança
     */
    public function render_security_dashboard_widget()
    {
        // Obter eventos de segurança recentes
        $events = $this->get_security_events(10);

        // Verificar se há ataques em andamento
        $attacks = $this->check_for_ongoing_attacks();

        // Exibir ataques em andamento
        if (!empty($attacks)) {
            echo '<div class="notice notice-error inline"><p><strong>' . __('Atenção: Possíveis ataques em andamento!', 'thabatta-adv') . '</strong></p>';
            echo '<ul>';

            foreach ($attacks as $attack) {
                echo '<li>' . sprintf(__('Tipo: %s, Contagem: %d', 'thabatta-adv'), $attack['type'], $attack['count']) . '</li>';
            }

            echo '</ul></div>';
        }

        // Exibir eventos recentes
        if (!empty($events)) {
            echo '<table class="widefat">';
            echo '<thead><tr>';
            echo '<th>' . __('Tipo', 'thabatta-adv') . '</th>';
            echo '<th>' . __('IP', 'thabatta-adv') . '</th>';
            echo '<th>' . __('Data/Hora', 'thabatta-adv') . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';

            foreach ($events as $event) {
                echo '<tr>';
                echo '<td>' . esc_html($event['type']) . '</td>';
                echo '<td>' . esc_html($event['ip']) . '</td>';
                echo '<td>' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $event['timestamp']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p>' . __('Nenhum evento de segurança registrado recentemente.', 'thabatta-adv') . '</p>';
        }

        // Exibir link para página de configurações de segurança
        echo '<p><a href="' . admin_url('admin.php?page=thabatta-security-settings') . '">' . __('Ver todos os eventos e configurações de segurança', 'thabatta-adv') . '</a></p>';
    }

    /**
     * Adicionar página de configurações de segurança
     */
    public function add_security_settings_page()
    {
        add_submenu_page(
            'options-general.php',
            __('Configurações de Segurança', 'thabatta-adv'),
            __('Segurança', 'thabatta-adv'),
            'manage_options',
            'thabatta-security-settings',
            array($this, 'render_security_settings_page')
        );
    }

    /**
     * Renderizar página de configurações de segurança
     */
    public function render_security_settings_page()
    {
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta página.', 'thabatta-adv'));
        }

        // Processar ações
        if (isset($_POST['thabatta_security_action']) && isset($_POST['thabatta_security_nonce']) && wp_verify_nonce($_POST['thabatta_security_nonce'], 'thabatta_security_action')) {
            $action = sanitize_text_field($_POST['thabatta_security_action']);

            switch ($action) {
                case 'clear_events':
                    // Limpar eventos de segurança
                    update_option('thabatta_security_events', array());
                    echo '<div class="notice notice-success"><p>' . __('Eventos de segurança limpos com sucesso.', 'thabatta-adv') . '</p></div>';
                    break;

                case 'clear_blocked_ips':
                    // Limpar IPs bloqueados
                    update_option('thabatta_blocked_ips', array());
                    echo '<div class="notice notice-success"><p>' . __('IPs bloqueados limpos com sucesso.', 'thabatta-adv') . '</p></div>';
                    break;

                case 'save_settings':
                    // Salvar configurações
                    $settings = array(
                        'login_attempts_limit' => isset($_POST['login_attempts_limit']) ? intval($_POST['login_attempts_limit']) : 5,
                        'login_block_duration' => isset($_POST['login_block_duration']) ? intval($_POST['login_block_duration']) : 3600,
                        'enable_security_headers' => isset($_POST['enable_security_headers']) ? 1 : 0,
                        'enable_csrf_protection' => isset($_POST['enable_csrf_protection']) ? 1 : 0,
                        'enable_xss_protection' => isset($_POST['enable_xss_protection']) ? 1 : 0,
                        'enable_sql_injection_protection' => isset($_POST['enable_sql_injection_protection']) ? 1 : 0,
                    );

                    update_option('thabatta_security_settings', $settings);
                    echo '<div class="notice notice-success"><p>' . __('Configurações salvas com sucesso.', 'thabatta-adv') . '</p></div>';
                    break;
            }
        }

        // Obter configurações
        $settings = get_option('thabatta_security_settings', array(
            'login_attempts_limit' => 5,
            'login_block_duration' => 3600,
            'enable_security_headers' => 1,
            'enable_csrf_protection' => 1,
            'enable_xss_protection' => 1,
            'enable_sql_injection_protection' => 1,
        ));

        // Obter eventos de segurança
        $events = $this->get_security_events(50);

        // Obter IPs bloqueados
        $blocked_ips = get_option('thabatta_blocked_ips', array());

        // Exibir página
        ?>
        <div class="wrap">
            <h1><?php _e('Configurações de Segurança', 'thabatta-adv'); ?></h1>
            
            <h2 class="nav-tab-wrapper">
                <a href="#settings" class="nav-tab nav-tab-active"><?php _e('Configurações', 'thabatta-adv'); ?></a>
                <a href="#events" class="nav-tab"><?php _e('Eventos', 'thabatta-adv'); ?></a>
                <a href="#blocked-ips" class="nav-tab"><?php _e('IPs Bloqueados', 'thabatta-adv'); ?></a>
            </h2>
            
            <div id="settings" class="tab-content">
                <form method="post" action="">
                    <?php wp_nonce_field('thabatta_security_action', 'thabatta_security_nonce'); ?>
                    <input type="hidden" name="thabatta_security_action" value="save_settings">
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Limite de Tentativas de Login', 'thabatta-adv'); ?></th>
                            <td>
                                <input type="number" name="login_attempts_limit" value="<?php echo esc_attr($settings['login_attempts_limit']); ?>" min="1" max="20">
                                <p class="description"><?php _e('Número máximo de tentativas de login antes de bloquear o IP.', 'thabatta-adv'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Duração do Bloqueio (segundos)', 'thabatta-adv'); ?></th>
                            <td>
                                <input type="number" name="login_block_duration" value="<?php echo esc_attr($settings['login_block_duration']); ?>" min="300" max="86400">
                                <p class="description"><?php _e('Duração do bloqueio de IP após exceder o limite de tentativas de login (em segundos).', 'thabatta-adv'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Habilitar Cabeçalhos de Segurança', 'thabatta-adv'); ?></th>
                            <td>
                                <input type="checkbox" name="enable_security_headers" value="1" <?php checked($settings['enable_security_headers'], 1); ?>>
                                <p class="description"><?php _e('Adicionar cabeçalhos de segurança HTTP para proteção contra ataques comuns.', 'thabatta-adv'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Habilitar Proteção CSRF', 'thabatta-adv'); ?></th>
                            <td>
                                <input type="checkbox" name="enable_csrf_protection" value="1" <?php checked($settings['enable_csrf_protection'], 1); ?>>
                                <p class="description"><?php _e('Adicionar proteção contra ataques CSRF (Cross-Site Request Forgery).', 'thabatta-adv'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Habilitar Proteção XSS', 'thabatta-adv'); ?></th>
                            <td>
                                <input type="checkbox" name="enable_xss_protection" value="1" <?php checked($settings['enable_xss_protection'], 1); ?>>
                                <p class="description"><?php _e('Adicionar proteção contra ataques XSS (Cross-Site Scripting).', 'thabatta-adv'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Habilitar Proteção contra Injeção SQL', 'thabatta-adv'); ?></th>
                            <td>
                                <input type="checkbox" name="enable_sql_injection_protection" value="1" <?php checked($settings['enable_sql_injection_protection'], 1); ?>>
                                <p class="description"><?php _e('Adicionar proteção contra ataques de injeção SQL.', 'thabatta-adv'); ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Salvar Configurações', 'thabatta-adv'); ?>">
                    </p>
                </form>
            </div>
            
            <div id="events" class="tab-content" style="display: none;">
                <h2><?php _e('Eventos de Segurança', 'thabatta-adv'); ?></h2>
                
                <form method="post" action="">
                    <?php wp_nonce_field('thabatta_security_action', 'thabatta_security_nonce'); ?>
                    <input type="hidden" name="thabatta_security_action" value="clear_events">
                    <p>
                        <input type="submit" class="button-secondary" value="<?php _e('Limpar Todos os Eventos', 'thabatta-adv'); ?>" onclick="return confirm('<?php _e('Tem certeza de que deseja limpar todos os eventos de segurança?', 'thabatta-adv'); ?>');">
                    </p>
                </form>
                
                <?php if (!empty($events)) : ?>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Tipo', 'thabatta-adv'); ?></th>
                                <th><?php _e('IP', 'thabatta-adv'); ?></th>
                                <th><?php _e('User Agent', 'thabatta-adv'); ?></th>
                                <th><?php _e('Data/Hora', 'thabatta-adv'); ?></th>
                                <th><?php _e('Detalhes', 'thabatta-adv'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event) : ?>
                                <tr>
                                    <td><?php echo esc_html($event['type']); ?></td>
                                    <td><?php echo esc_html($event['ip']); ?></td>
                                    <td><?php echo esc_html($event['user_agent']); ?></td>
                                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $event['timestamp']); ?></td>
                                    <td>
                                        <?php if (!empty($event['data'])) : ?>
                                            <pre><?php print_r($event['data']); ?></pre>
                                        <?php else : ?>
                                            <?php _e('Nenhum detalhe disponível', 'thabatta-adv'); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php _e('Nenhum evento de segurança registrado.', 'thabatta-adv'); ?></p>
                <?php endif; ?>
            </div>
            
            <div id="blocked-ips" class="tab-content" style="display: none;">
                <h2><?php _e('IPs Bloqueados', 'thabatta-adv'); ?></h2>
                
                <form method="post" action="">
                    <?php wp_nonce_field('thabatta_security_action', 'thabatta_security_nonce'); ?>
                    <input type="hidden" name="thabatta_security_action" value="clear_blocked_ips">
                    <p>
                        <input type="submit" class="button-secondary" value="<?php _e('Limpar Todos os IPs Bloqueados', 'thabatta-adv'); ?>" onclick="return confirm('<?php _e('Tem certeza de que deseja limpar todos os IPs bloqueados?', 'thabatta-adv'); ?>');">
                    </p>
                </form>
                
                <?php if (!empty($blocked_ips)) : ?>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('IP', 'thabatta-adv'); ?></th>
                                <th><?php _e('Bloqueado em', 'thabatta-adv'); ?></th>
                                <th><?php _e('Expira em', 'thabatta-adv'); ?></th>
                                <th><?php _e('Status', 'thabatta-adv'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($blocked_ips as $ip => $data) : ?>
                                <tr>
                                    <td><?php echo esc_html($ip); ?></td>
                                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $data['blocked_at']); ?></td>
                                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $data['expires']); ?></td>
                                    <td>
                                        <?php if ($data['expires'] > time()) : ?>
                                            <span style="color: red;"><?php _e('Bloqueado', 'thabatta-adv'); ?></span>
                                        <?php else : ?>
                                            <span style="color: green;"><?php _e('Expirado', 'thabatta-adv'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php _e('Nenhum IP bloqueado.', 'thabatta-adv'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Tabs
            $('.nav-tab-wrapper a').on('click', function(e) {
                e.preventDefault();
                
                // Hide all tab content
                $('.tab-content').hide();
                
                // Remove active class from all tabs
                $('.nav-tab-wrapper a').removeClass('nav-tab-active');
                
                // Show selected tab content
                $($(this).attr('href')).show();
                
                // Add active class to selected tab
                $(this).addClass('nav-tab-active');
            });
        });
        </script>
        <?php
    }
}

// Inicializar a classe
$thabatta_security_features = new Thabatta_Security_Features();

// Adicionar widget de dashboard
add_action('wp_dashboard_setup', array($thabatta_security_features, 'add_security_dashboard_widget'));

// Adicionar página de configurações
add_action('admin_menu', array($thabatta_security_features, 'add_security_settings_page'));

// Agendar limpeza de eventos antigos
if (!wp_next_scheduled('thabatta_clean_security_events')) {
    wp_schedule_event(time(), 'daily', 'thabatta_clean_security_events');
}
add_action('thabatta_clean_security_events', array($thabatta_security_features, 'clean_old_security_events'));
