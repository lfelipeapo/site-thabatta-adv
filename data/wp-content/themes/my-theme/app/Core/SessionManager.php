<?php
/**
 * Classe SessionManager para gerenciamento de sessões
 * 
 * Responsável por controlar sessões customizadas (iniciar, destruir, persistir dados),
 * integrada à autenticação do WordPress.
 * 
 * @package WPFramework\Core
 */

namespace WPFramework\Core;

class SessionManager
{
    /**
     * Instância singleton
     * 
     * @var SessionManager
     */
    private static $instance = null;
    
    /**
     * Prefixo para as chaves de sessão
     * 
     * @var string
     */
    private $prefix = 'wpframework_';
    
    /**
     * Indica se a sessão foi iniciada
     * 
     * @var bool
     */
    private $started = false;

    /**
     * Inicializa o SessionManager
     * 
     * @return SessionManager
     */
    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Construtor privado para padrão singleton
     */
    private function __construct()
    {
        // Registra hooks para iniciar e encerrar a sessão
        add_action('init', [$this, 'startSession'], 1);
        add_action('wp_logout', [$this, 'destroySession']);
        add_action('wp_login', [$this, 'regenerateSession']);
        add_action('shutdown', [$this, 'writeSession']);
    }

    /**
     * Inicia a sessão
     */
    public function startSession()
    {
        if ($this->started) {
            return;
        }
        
        // Não inicia sessão para bots
        if ($this->isBot()) {
            return;
        }
        
        // Não inicia sessão para requisições AJAX ou REST
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }
        
        // Inicia a sessão se ainda não estiver ativa
        if (session_status() === PHP_SESSION_NONE) {
            // Define cookies seguros em HTTPS
            $secure = is_ssl();
            $httponly = true;
            
            // Define os parâmetros do cookie
            $cookiepath = defined('COOKIEPATH') ? COOKIEPATH : '/';
            $cookiedomain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => $cookiepath,
                'domain' => $cookiedomain,
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax'
            ]);
            
            session_start();
            $this->started = true;
            
            // Regenera o ID da sessão se for muito antigo (a cada 30 minutos)
            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
            } elseif (time() - $_SESSION['last_regeneration'] > 1800) {
                $this->regenerateSession();
            }
        }
    }

    /**
     * Verifica se o usuário é um bot
     * 
     * @return bool
     */
    private function isBot()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $bots = ['bot', 'crawler', 'spider', 'slurp', 'yahoo', 'googlebot'];
        
        foreach ($bots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Regenera o ID da sessão
     */
    public function regenerateSession()
    {
        if (!$this->started || session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        
        // Salva os dados da sessão atual
        $old_session_data = $_SESSION;
        
        // Regenera o ID da sessão
        session_regenerate_id(true);
        
        // Restaura os dados da sessão
        $_SESSION = $old_session_data;
        
        // Atualiza o timestamp de regeneração
        $_SESSION['last_regeneration'] = time();
    }

    /**
     * Destrói a sessão
     */
    public function destroySession()
    {
        if (!$this->started || session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        
        // Limpa os dados da sessão
        $_SESSION = [];
        
        // Destrói o cookie da sessão
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        // Destrói a sessão
        session_destroy();
        $this->started = false;
    }

    /**
     * Escreve a sessão no final da execução
     */
    public function writeSession()
    {
        if ($this->started && session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    /**
     * Define um valor na sessão
     * 
     * @param string $key Chave
     * @param mixed $value Valor
     * @return SessionManager
     */
    public function set($key, $value)
    {
        if (!$this->started) {
            $this->startSession();
        }
        
        $_SESSION[$this->prefix . $key] = $value;
        
        return $this;
    }

    /**
     * Obtém um valor da sessão
     * 
     * @param string $key Chave
     * @param mixed $default Valor padrão
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!$this->started) {
            $this->startSession();
        }
        
        return isset($_SESSION[$this->prefix . $key]) ? $_SESSION[$this->prefix . $key] : $default;
    }

    /**
     * Verifica se uma chave existe na sessão
     * 
     * @param string $key Chave
     * @return bool
     */
    public function has($key)
    {
        if (!$this->started) {
            $this->startSession();
        }
        
        return isset($_SESSION[$this->prefix . $key]);
    }

    /**
     * Remove uma chave da sessão
     * 
     * @param string $key Chave
     * @return SessionManager
     */
    public function remove($key)
    {
        if (!$this->started) {
            $this->startSession();
        }
        
        if (isset($_SESSION[$this->prefix . $key])) {
            unset($_SESSION[$this->prefix . $key]);
        }
        
        return $this;
    }

    /**
     * Limpa todos os dados da sessão
     * 
     * @return SessionManager
     */
    public function clear()
    {
        if (!$this->started) {
            $this->startSession();
        }
        
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, $this->prefix) === 0) {
                unset($_SESSION[$key]);
            }
        }
        
        return $this;
    }

    /**
     * Define o prefixo para as chaves de sessão
     * 
     * @param string $prefix Prefixo
     * @return SessionManager
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        
        return $this;
    }

    /**
     * Obtém o prefixo para as chaves de sessão
     * 
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Define um valor flash na sessão (disponível apenas para a próxima requisição)
     * 
     * @param string $key Chave
     * @param mixed $value Valor
     * @return SessionManager
     */
    public function flash($key, $value)
    {
        $this->set('flash.' . $key, $value);
        
        if (!$this->has('flash.keys')) {
            $this->set('flash.keys', [$key]);
        } else {
            $keys = $this->get('flash.keys', []);
            $keys[] = $key;
            $this->set('flash.keys', $keys);
        }
        
        return $this;
    }

    /**
     * Obtém um valor flash da sessão
     * 
     * @param string $key Chave
     * @param mixed $default Valor padrão
     * @return mixed
     */
    public function getFlash($key, $default = null)
    {
        return $this->get('flash.' . $key, $default);
    }

    /**
     * Limpa os valores flash da sessão
     */
    public function clearFlash()
    {
        $keys = $this->get('flash.keys', []);
        
        foreach ($keys as $key) {
            $this->remove('flash.' . $key);
        }
        
        $this->remove('flash.keys');
    }
}
