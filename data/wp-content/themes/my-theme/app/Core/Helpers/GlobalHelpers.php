<?php
/**
 * Helpers globais para o tema
 * 
 * Funções auxiliares globais para uso em todo o tema
 * 
 * @package WPFramework\Core\Helpers
 */

namespace WPFramework\Core\Helpers;

/**
 * Classe de helpers globais
 */
class GlobalHelpers
{
    /**
     * Sanitiza uma entrada de texto
     * 
     * @param string $input Texto a ser sanitizado
     * @return string
     */
    public static function sanitizeText($input)
    {
        return sanitize_text_field($input);
    }
    
    /**
     * Sanitiza um e-mail
     * 
     * @param string $email E-mail a ser sanitizado
     * @return string
     */
    public static function sanitizeEmail($email)
    {
        return sanitize_email($email);
    }
    
    /**
     * Sanitiza uma URL
     * 
     * @param string $url URL a ser sanitizada
     * @return string
     */
    public static function sanitizeUrl($url)
    {
        return esc_url_raw($url);
    }
    
    /**
     * Sanitiza um nome de arquivo
     * 
     * @param string $filename Nome do arquivo a ser sanitizado
     * @return string
     */
    public static function sanitizeFilename($filename)
    {
        return sanitize_file_name($filename);
    }
    
    /**
     * Sanitiza um HTML
     * 
     * @param string $html HTML a ser sanitizado
     * @param array $allowed_html Tags HTML permitidas
     * @return string
     */
    public static function sanitizeHtml($html, $allowed_html = null)
    {
        if ($allowed_html === null) {
            $allowed_html = wp_kses_allowed_html('post');
        }
        
        return wp_kses($html, $allowed_html);
    }
    
    /**
     * Valida um e-mail
     * 
     * @param string $email E-mail a ser validado
     * @return bool
     */
    public static function isValidEmail($email)
    {
        return is_email($email);
    }
    
    /**
     * Valida uma URL
     * 
     * @param string $url URL a ser validada
     * @return bool
     */
    public static function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Obtém um valor de $_GET
     * 
     * @param string $key Chave do parâmetro
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    public static function getQueryParam($key, $default = null)
    {
        return isset($_GET[$key]) ? self::sanitizeText($_GET[$key]) : $default;
    }
    
    /**
     * Obtém um valor de $_POST
     * 
     * @param string $key Chave do parâmetro
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    public static function getPostParam($key, $default = null)
    {
        return isset($_POST[$key]) ? self::sanitizeText($_POST[$key]) : $default;
    }
    
    /**
     * Obtém um valor de $_REQUEST
     * 
     * @param string $key Chave do parâmetro
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    public static function getRequestParam($key, $default = null)
    {
        return isset($_REQUEST[$key]) ? self::sanitizeText($_REQUEST[$key]) : $default;
    }
    
    /**
     * Obtém um valor de $_COOKIE
     * 
     * @param string $key Chave do cookie
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    public static function getCookieParam($key, $default = null)
    {
        return isset($_COOKIE[$key]) ? self::sanitizeText($_COOKIE[$key]) : $default;
    }
    
    /**
     * Obtém um valor de $_SESSION
     * 
     * @param string $key Chave da sessão
     * @param mixed $default Valor padrão se não existir
     * @return mixed
     */
    public static function getSessionParam($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * Obtém um valor de $_FILES
     * 
     * @param string $key Chave do arquivo
     * @return array|null
     */
    public static function getFileParam($key)
    {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }
    
    /**
     * Formata uma data
     * 
     * @param string $date Data a ser formatada
     * @param string $format Formato da data
     * @return string
     */
    public static function formatDate($date, $format = 'd/m/Y')
    {
        return date_i18n($format, strtotime($date));
    }
    
    /**
     * Formata um valor monetário
     * 
     * @param float $value Valor a ser formatado
     * @param string $currency Símbolo da moeda
     * @return string
     */
    public static function formatMoney($value, $currency = 'R$')
    {
        return $currency . ' ' . number_format($value, 2, ',', '.');
    }
    
    /**
     * Trunca um texto
     * 
     * @param string $text Texto a ser truncado
     * @param int $length Comprimento máximo
     * @param string $suffix Sufixo para indicar truncamento
     * @return string
     */
    public static function truncateText($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }
    
    /**
     * Gera um slug a partir de um texto
     * 
     * @param string $text Texto a ser convertido em slug
     * @return string
     */
    public static function slugify($text)
    {
        return sanitize_title($text);
    }
    
    /**
     * Verifica se uma string começa com outra
     * 
     * @param string $haystack String a ser verificada
     * @param string $needle String a ser procurada
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
    
    /**
     * Verifica se uma string termina com outra
     * 
     * @param string $haystack String a ser verificada
     * @param string $needle String a ser procurada
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }
    
    /**
     * Obtém a URL atual
     * 
     * @return string
     */
    public static function getCurrentUrl()
    {
        global $wp;
        return home_url(add_query_arg([], $wp->request));
    }
    
    /**
     * Verifica se a requisição atual é AJAX
     * 
     * @return bool
     */
    public static function isAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }
    
    /**
     * Verifica se a requisição atual é REST
     * 
     * @return bool
     */
    public static function isRest()
    {
        return defined('REST_REQUEST') && REST_REQUEST;
    }
    
    /**
     * Verifica se a requisição atual é via POST
     * 
     * @return bool
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verifica se a requisição atual é via GET
     * 
     * @return bool
     */
    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Redireciona para uma URL
     * 
     * @param string $url URL para redirecionamento
     * @param int $status Código de status HTTP
     * @return void
     */
    public static function redirect($url, $status = 302)
    {
        wp_redirect($url, $status);
        exit;
    }
    
    /**
     * Obtém o IP do usuário
     * 
     * @return string
     */
    public static function getUserIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
    
    /**
     * Obtém o user agent do usuário
     * 
     * @return string
     */
    public static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    
    /**
     * Obtém o idioma do usuário
     * 
     * @return string
     */
    public static function getUserLanguage()
    {
        return isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : '';
    }
    
    /**
     * Obtém o referer do usuário
     * 
     * @return string
     */
    public static function getReferer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }
    
    /**
     * Gera um token aleatório
     * 
     * @param int $length Comprimento do token
     * @return string
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Verifica se o usuário está logado
     * 
     * @return bool
     */
    public static function isUserLoggedIn()
    {
        return is_user_logged_in();
    }
    
    /**
     * Obtém o usuário atual
     * 
     * @return \WP_User|false
     */
    public static function getCurrentUser()
    {
        return wp_get_current_user();
    }
    
    /**
     * Verifica se o usuário tem uma capacidade
     * 
     * @param string $capability Capacidade a ser verificada
     * @return bool
     */
    public static function userCan($capability)
    {
        return current_user_can($capability);
    }
    
    /**
     * Obtém o ID do post atual
     * 
     * @return int
     */
    public static function getCurrentPostId()
    {
        return get_the_ID();
    }
    
    /**
     * Obtém o post atual
     * 
     * @return \WP_Post|null
     */
    public static function getCurrentPost()
    {
        return get_post();
    }
    
    /**
     * Obtém o tipo do post atual
     * 
     * @return string
     */
    public static function getCurrentPostType()
    {
        return get_post_type();
    }
    
    /**
     * Verifica se a página atual é uma página específica
     * 
     * @param string $page Nome da página
     * @return bool
     */
    public static function isPage($page)
    {
        return is_page($page);
    }
    
    /**
     * Verifica se a página atual é um post específico
     * 
     * @param string $post Nome do post
     * @return bool
     */
    public static function isSinglePost($post)
    {
        return is_single($post);
    }
    
    /**
     * Verifica se a página atual é um tipo de post específico
     * 
     * @param string $post_type Tipo de post
     * @return bool
     */
    public static function isPostType($post_type)
    {
        return is_post_type_archive($post_type);
    }
    
    /**
     * Verifica se a página atual é a página inicial
     * 
     * @return bool
     */
    public static function isHome()
    {
        return is_home() || is_front_page();
    }
    
    /**
     * Verifica se a página atual é uma página de arquivo
     * 
     * @return bool
     */
    public static function isArchive()
    {
        return is_archive();
    }
    
    /**
     * Verifica se a página atual é uma página de categoria
     * 
     * @param string $category Nome da categoria
     * @return bool
     */
    public static function isCategory($category = '')
    {
        return is_category($category);
    }
    
    /**
     * Verifica se a página atual é uma página de tag
     * 
     * @param string $tag Nome da tag
     * @return bool
     */
    public static function isTag($tag = '')
    {
        return is_tag($tag);
    }
    
    /**
     * Verifica se a página atual é uma página de taxonomia
     * 
     * @param string $taxonomy Nome da taxonomia
     * @param string $term Nome do termo
     * @return bool
     */
    public static function isTaxonomy($taxonomy, $term = '')
    {
        return is_tax($taxonomy, $term);
    }
    
    /**
     * Verifica se a página atual é uma página de busca
     * 
     * @return bool
     */
    public static function isSearch()
    {
        return is_search();
    }
    
    /**
     * Verifica se a página atual é uma página 404
     * 
     * @return bool
     */
    public static function is404()
    {
        return is_404();
    }
    
    /**
     * Verifica se a página atual é uma página de autor
     * 
     * @param string $author Nome do autor
     * @return bool
     */
    public static function isAuthor($author = '')
    {
        return is_author($author);
    }
    
    /**
     * Verifica se a página atual é uma página de data
     * 
     * @return bool
     */
    public static function isDate()
    {
        return is_date();
    }
    
    /**
     * Verifica se a página atual é uma página de anexo
     * 
     * @return bool
     */
    public static function isAttachment()
    {
        return is_attachment();
    }
    
    /**
     * Verifica se a página atual é uma página de feed
     * 
     * @return bool
     */
    public static function isFeed()
    {
        return is_feed();
    }
    
    /**
     * Verifica se a página atual é uma página de comentário
     * 
     * @return bool
     */
    public static function isComment()
    {
        return is_comment_feed();
    }
    
    /**
     * Verifica se a página atual é uma página de trackback
     * 
     * @return bool
     */
    public static function isTrackback()
    {
        return is_trackback();
    }
    
    /**
     * Verifica se a página atual é uma página de preview
     * 
     * @return bool
     */
    public static function isPreview()
    {
        return is_preview();
    }
    
    /**
     * Verifica se a página atual é uma página de admin
     * 
     * @return bool
     */
    public static function isAdmin()
    {
        return is_admin();
    }
    
    /**
     * Verifica se a página atual é uma página de login
     * 
     * @return bool
     */
    public static function isLogin()
    {
        return in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']);
    }
    
    /**
     * Verifica se a página atual é uma página de customização
     * 
     * @return bool
     */
    public static function isCustomize()
    {
        return is_customize_preview();
    }
    
    /**
     * Verifica se a página atual é uma página de REST
     * 
     * @return bool
     */
    public static function isRestApi()
    {
        return self::isRest();
    }
    
    /**
     * Verifica se a página atual é uma página de AJAX
     * 
     * @return bool
     */
    public static function isAjaxRequest()
    {
        return self::isAjax();
    }
    
    /**
     * Verifica se a página atual é uma página de CLI
     * 
     * @return bool
     */
    public static function isCli()
    {
        return defined('WP_CLI') && WP_CLI;
    }
    
    /**
     * Verifica se a página atual é uma página de CRON
     * 
     * @return bool
     */
    public static function isCron()
    {
        return defined('DOING_CRON') && DOING_CRON;
    }
    
    /**
     * Verifica se a página atual é uma página de instalação
     * 
     * @return bool
     */
    public static function isInstall()
    {
        return defined('WP_INSTALLING') && WP_INSTALLING;
    }
    
    /**
     * Verifica se a página atual é uma página de XML-RPC
     * 
     * @return bool
     */
    public static function isXmlRpc()
    {
        return defined('XMLRPC_REQUEST') && XMLRPC_REQUEST;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_DEBUG
     * 
     * @return bool
     */
    public static function isDebug()
    {
        return defined('WP_DEBUG') && WP_DEBUG;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_DEBUG_LOG
     * 
     * @return bool
     */
    public static function isDebugLog()
    {
        return defined('WP_DEBUG_LOG') && WP_DEBUG_LOG;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_DEBUG_DISPLAY
     * 
     * @return bool
     */
    public static function isDebugDisplay()
    {
        return defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY;
    }
    
    /**
     * Verifica se a página atual é uma página de SCRIPT_DEBUG
     * 
     * @return bool
     */
    public static function isScriptDebug()
    {
        return defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;
    }
    
    /**
     * Verifica se a página atual é uma página de SAVEQUERIES
     * 
     * @return bool
     */
    public static function isSaveQueries()
    {
        return defined('SAVEQUERIES') && SAVEQUERIES;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_CACHE
     * 
     * @return bool
     */
    public static function isCache()
    {
        return defined('WP_CACHE') && WP_CACHE;
    }
    
    /**
     * Verifica se a página atual é uma página de COMPRESS_CSS
     * 
     * @return bool
     */
    public static function isCompressCss()
    {
        return defined('COMPRESS_CSS') && COMPRESS_CSS;
    }
    
    /**
     * Verifica se a página atual é uma página de COMPRESS_SCRIPTS
     * 
     * @return bool
     */
    public static function isCompressScripts()
    {
        return defined('COMPRESS_SCRIPTS') && COMPRESS_SCRIPTS;
    }
    
    /**
     * Verifica se a página atual é uma página de CONCATENATE_SCRIPTS
     * 
     * @return bool
     */
    public static function isConcatenateScripts()
    {
        return defined('CONCATENATE_SCRIPTS') && CONCATENATE_SCRIPTS;
    }
    
    /**
     * Verifica se a página atual é uma página de ENFORCE_GZIP
     * 
     * @return bool
     */
    public static function isEnforceGzip()
    {
        return defined('ENFORCE_GZIP') && ENFORCE_GZIP;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_LOCAL_DEV
     * 
     * @return bool
     */
    public static function isLocalDev()
    {
        return defined('WP_LOCAL_DEV') && WP_LOCAL_DEV;
    }
    
    /**
     * Verifica se a página atual é uma página de SUNRISE
     * 
     * @return bool
     */
    public static function isSunrise()
    {
        return defined('SUNRISE') && SUNRISE;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_MEMORY_LIMIT
     * 
     * @return string
     */
    public static function getMemoryLimit()
    {
        return defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : '40M';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_MAX_MEMORY_LIMIT
     * 
     * @return string
     */
    public static function getMaxMemoryLimit()
    {
        return defined('WP_MAX_MEMORY_LIMIT') ? WP_MAX_MEMORY_LIMIT : '256M';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_CONTENT_DIR
     * 
     * @return string
     */
    public static function getContentDir()
    {
        return defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR : ABSPATH . 'wp-content';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_CONTENT_URL
     * 
     * @return string
     */
    public static function getContentUrl()
    {
        return defined('WP_CONTENT_URL') ? WP_CONTENT_URL : get_option('siteurl') . '/wp-content';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_PLUGIN_DIR
     * 
     * @return string
     */
    public static function getPluginDir()
    {
        return defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : self::getContentDir() . '/plugins';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_PLUGIN_URL
     * 
     * @return string
     */
    public static function getPluginUrl()
    {
        return defined('WP_PLUGIN_URL') ? WP_PLUGIN_URL : self::getContentUrl() . '/plugins';
    }
    
    /**
     * Verifica se a página atual é uma página de WPMU_PLUGIN_DIR
     * 
     * @return string
     */
    public static function getMuPluginDir()
    {
        return defined('WPMU_PLUGIN_DIR') ? WPMU_PLUGIN_DIR : self::getContentDir() . '/mu-plugins';
    }
    
    /**
     * Verifica se a página atual é uma página de WPMU_PLUGIN_URL
     * 
     * @return string
     */
    public static function getMuPluginUrl()
    {
        return defined('WPMU_PLUGIN_URL') ? WPMU_PLUGIN_URL : self::getContentUrl() . '/mu-plugins';
    }
    
    /**
     * Verifica se a página atual é uma página de COOKIEPATH
     * 
     * @return string
     */
    public static function getCookiePath()
    {
        return defined('COOKIEPATH') ? COOKIEPATH : '/';
    }
    
    /**
     * Verifica se a página atual é uma página de SITECOOKIEPATH
     * 
     * @return string
     */
    public static function getSiteCookiePath()
    {
        return defined('SITECOOKIEPATH') ? SITECOOKIEPATH : '/';
    }
    
    /**
     * Verifica se a página atual é uma página de ADMIN_COOKIE_PATH
     * 
     * @return string
     */
    public static function getAdminCookiePath()
    {
        return defined('ADMIN_COOKIE_PATH') ? ADMIN_COOKIE_PATH : '/wp-admin';
    }
    
    /**
     * Verifica se a página atual é uma página de PLUGINS_COOKIE_PATH
     * 
     * @return string
     */
    public static function getPluginsCookiePath()
    {
        return defined('PLUGINS_COOKIE_PATH') ? PLUGINS_COOKIE_PATH : '/wp-content/plugins';
    }
    
    /**
     * Verifica se a página atual é uma página de COOKIE_DOMAIN
     * 
     * @return string
     */
    public static function getCookieDomain()
    {
        return defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
    }
    
    /**
     * Verifica se a página atual é uma página de COOKIEHASH
     * 
     * @return string
     */
    public static function getCookieHash()
    {
        return defined('COOKIEHASH') ? COOKIEHASH : md5(get_option('siteurl'));
    }
    
    /**
     * Verifica se a página atual é uma página de AUTH_COOKIE
     * 
     * @return string
     */
    public static function getAuthCookie()
    {
        return defined('AUTH_COOKIE') ? AUTH_COOKIE : 'wordpress_' . self::getCookieHash();
    }
    
    /**
     * Verifica se a página atual é uma página de SECURE_AUTH_COOKIE
     * 
     * @return string
     */
    public static function getSecureAuthCookie()
    {
        return defined('SECURE_AUTH_COOKIE') ? SECURE_AUTH_COOKIE : 'wordpress_sec_' . self::getCookieHash();
    }
    
    /**
     * Verifica se a página atual é uma página de LOGGED_IN_COOKIE
     * 
     * @return string
     */
    public static function getLoggedInCookie()
    {
        return defined('LOGGED_IN_COOKIE') ? LOGGED_IN_COOKIE : 'wordpress_logged_in_' . self::getCookieHash();
    }
    
    /**
     * Verifica se a página atual é uma página de USER_COOKIE
     * 
     * @return string
     */
    public static function getUserCookie()
    {
        return defined('USER_COOKIE') ? USER_COOKIE : 'wordpressuser_' . self::getCookieHash();
    }
    
    /**
     * Verifica se a página atual é uma página de PASS_COOKIE
     * 
     * @return string
     */
    public static function getPassCookie()
    {
        return defined('PASS_COOKIE') ? PASS_COOKIE : 'wordpresspass_' . self::getCookieHash();
    }
    
    /**
     * Verifica se a página atual é uma página de AUTH_COOKIE_NAME
     * 
     * @return string
     */
    public static function getAuthCookieName()
    {
        return defined('AUTH_COOKIE_NAME') ? AUTH_COOKIE_NAME : 'wordpress_' . self::getCookieHash();
    }
    
    /**
     * Verifica se a página atual é uma página de AUTH_COOKIE_EXPIRATION
     * 
     * @return int
     */
    public static function getAuthCookieExpiration()
    {
        return defined('AUTH_COOKIE_EXPIRATION') ? AUTH_COOKIE_EXPIRATION : 172800; // 2 days
    }
    
    /**
     * Verifica se a página atual é uma página de RECOVERY_MODE_COOKIE
     * 
     * @return string
     */
    public static function getRecoveryModeCookie()
    {
        return defined('RECOVERY_MODE_COOKIE') ? RECOVERY_MODE_COOKIE : 'wordpress_rec_' . self::getCookieHash();
    }
    
    /**
     * Verifica se a página atual é uma página de FORCE_SSL_ADMIN
     * 
     * @return bool
     */
    public static function isForceSslAdmin()
    {
        return defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN;
    }
    
    /**
     * Verifica se a página atual é uma página de FORCE_SSL_LOGIN
     * 
     * @return bool
     */
    public static function isForceSslLogin()
    {
        return defined('FORCE_SSL_LOGIN') && FORCE_SSL_LOGIN;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_ENVIRONMENT_TYPE
     * 
     * @return string
     */
    public static function getEnvironmentType()
    {
        return defined('WP_ENVIRONMENT_TYPE') ? WP_ENVIRONMENT_TYPE : 'production';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_DEVELOPMENT_MODE
     * 
     * @return string
     */
    public static function getDevelopmentMode()
    {
        return defined('WP_DEVELOPMENT_MODE') ? WP_DEVELOPMENT_MODE : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_CACHE_KEY_SALT
     * 
     * @return string
     */
    public static function getCacheKeySalt()
    {
        return defined('WP_CACHE_KEY_SALT') ? WP_CACHE_KEY_SALT : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_ALLOW_MULTISITE
     * 
     * @return bool
     */
    public static function isAllowMultisite()
    {
        return defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE;
    }
    
    /**
     * Verifica se a página atual é uma página de MULTISITE
     * 
     * @return bool
     */
    public static function isMultisite()
    {
        return defined('MULTISITE') && MULTISITE;
    }
    
    /**
     * Verifica se a página atual é uma página de SUBDOMAIN_INSTALL
     * 
     * @return bool
     */
    public static function isSubdomainInstall()
    {
        return defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL;
    }
    
    /**
     * Verifica se a página atual é uma página de DOMAIN_CURRENT_SITE
     * 
     * @return string
     */
    public static function getDomainCurrentSite()
    {
        return defined('DOMAIN_CURRENT_SITE') ? DOMAIN_CURRENT_SITE : '';
    }
    
    /**
     * Verifica se a página atual é uma página de PATH_CURRENT_SITE
     * 
     * @return string
     */
    public static function getPathCurrentSite()
    {
        return defined('PATH_CURRENT_SITE') ? PATH_CURRENT_SITE : '/';
    }
    
    /**
     * Verifica se a página atual é uma página de SITE_ID_CURRENT_SITE
     * 
     * @return int
     */
    public static function getSiteIdCurrentSite()
    {
        return defined('SITE_ID_CURRENT_SITE') ? SITE_ID_CURRENT_SITE : 1;
    }
    
    /**
     * Verifica se a página atual é uma página de BLOG_ID_CURRENT_SITE
     * 
     * @return int
     */
    public static function getBlogIdCurrentSite()
    {
        return defined('BLOG_ID_CURRENT_SITE') ? BLOG_ID_CURRENT_SITE : 1;
    }
    
    /**
     * Verifica se a página atual é uma página de NOBLOGREDIRECT
     * 
     * @return string
     */
    public static function getNoBlogRedirect()
    {
        return defined('NOBLOGREDIRECT') ? NOBLOGREDIRECT : '';
    }
    
    /**
     * Verifica se a página atual é uma página de UPLOADBLOGSDIR
     * 
     * @return string
     */
    public static function getUploadBlogsDir()
    {
        return defined('UPLOADBLOGSDIR') ? UPLOADBLOGSDIR : 'wp-content/blogs.dir';
    }
    
    /**
     * Verifica se a página atual é uma página de UPLOADS
     * 
     * @return string
     */
    public static function getUploads()
    {
        return defined('UPLOADS') ? UPLOADS : 'wp-content/uploads';
    }
    
    /**
     * Verifica se a página atual é uma página de BLOGUPLOADDIR
     * 
     * @return string
     */
    public static function getBlogUploadDir()
    {
        return defined('BLOGUPLOADDIR') ? BLOGUPLOADDIR : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_DEFAULT_THEME
     * 
     * @return string
     */
    public static function getDefaultTheme()
    {
        return defined('WP_DEFAULT_THEME') ? WP_DEFAULT_THEME : 'twentytwentythree';
    }
    
    /**
     * Verifica se a página atual é uma página de DISALLOW_FILE_EDIT
     * 
     * @return bool
     */
    public static function isDisallowFileEdit()
    {
        return defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT;
    }
    
    /**
     * Verifica se a página atual é uma página de DISALLOW_FILE_MODS
     * 
     * @return bool
     */
    public static function isDisallowFileMods()
    {
        return defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_AUTO_UPDATE_CORE
     * 
     * @return bool|string
     */
    public static function getAutoUpdateCore()
    {
        return defined('WP_AUTO_UPDATE_CORE') ? WP_AUTO_UPDATE_CORE : false;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_POST_REVISIONS
     * 
     * @return bool|int
     */
    public static function getPostRevisions()
    {
        return defined('WP_POST_REVISIONS') ? WP_POST_REVISIONS : true;
    }
    
    /**
     * Verifica se a página atual é uma página de AUTOSAVE_INTERVAL
     * 
     * @return int
     */
    public static function getAutosaveInterval()
    {
        return defined('AUTOSAVE_INTERVAL') ? AUTOSAVE_INTERVAL : 60;
    }
    
    /**
     * Verifica se a página atual é uma página de EMPTY_TRASH_DAYS
     * 
     * @return int
     */
    public static function getEmptyTrashDays()
    {
        return defined('EMPTY_TRASH_DAYS') ? EMPTY_TRASH_DAYS : 30;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_CRON_LOCK_TIMEOUT
     * 
     * @return int
     */
    public static function getCronLockTimeout()
    {
        return defined('WP_CRON_LOCK_TIMEOUT') ? WP_CRON_LOCK_TIMEOUT : 60;
    }
    
    /**
     * Verifica se a página atual é uma página de MEDIA_TRASH
     * 
     * @return bool
     */
    public static function isMediaTrash()
    {
        return defined('MEDIA_TRASH') && MEDIA_TRASH;
    }
    
    /**
     * Verifica se a página atual é uma página de SHORTINIT
     * 
     * @return bool
     */
    public static function isShortInit()
    {
        return defined('SHORTINIT') && SHORTINIT;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_FEATURE_BETTER_PASSWORDS
     * 
     * @return bool
     */
    public static function isBetterPasswords()
    {
        return defined('WP_FEATURE_BETTER_PASSWORDS') ? WP_FEATURE_BETTER_PASSWORDS : true;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_ACCESSIBLE_HOSTS
     * 
     * @return string
     */
    public static function getAccessibleHosts()
    {
        return defined('WP_ACCESSIBLE_HOSTS') ? WP_ACCESSIBLE_HOSTS : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_HTTP_BLOCK_EXTERNAL
     * 
     * @return bool
     */
    public static function isHttpBlockExternal()
    {
        return defined('WP_HTTP_BLOCK_EXTERNAL') && WP_HTTP_BLOCK_EXTERNAL;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_PROXY_HOST
     * 
     * @return string
     */
    public static function getProxyHost()
    {
        return defined('WP_PROXY_HOST') ? WP_PROXY_HOST : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_PROXY_PORT
     * 
     * @return int
     */
    public static function getProxyPort()
    {
        return defined('WP_PROXY_PORT') ? WP_PROXY_PORT : 0;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_PROXY_USERNAME
     * 
     * @return string
     */
    public static function getProxyUsername()
    {
        return defined('WP_PROXY_USERNAME') ? WP_PROXY_USERNAME : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_PROXY_PASSWORD
     * 
     * @return string
     */
    public static function getProxyPassword()
    {
        return defined('WP_PROXY_PASSWORD') ? WP_PROXY_PASSWORD : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_PROXY_BYPASS_HOSTS
     * 
     * @return string
     */
    public static function getProxyBypassHosts()
    {
        return defined('WP_PROXY_BYPASS_HOSTS') ? WP_PROXY_BYPASS_HOSTS : 'localhost';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_LANG_DIR
     * 
     * @return string
     */
    public static function getLangDir()
    {
        return defined('WP_LANG_DIR') ? WP_LANG_DIR : self::getContentDir() . '/languages';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_DISABLE_FATAL_ERROR_HANDLER
     * 
     * @return bool
     */
    public static function isDisableFatalErrorHandler()
    {
        return defined('WP_DISABLE_FATAL_ERROR_HANDLER') && WP_DISABLE_FATAL_ERROR_HANDLER;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_CACHE_KEY_SALT
     * 
     * @return string
     */
    public static function getCacheSalt()
    {
        return defined('WP_CACHE_KEY_SALT') ? WP_CACHE_KEY_SALT : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_HOST
     * 
     * @return string
     */
    public static function getRedisHost()
    {
        return defined('WP_REDIS_HOST') ? WP_REDIS_HOST : '127.0.0.1';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_PORT
     * 
     * @return int
     */
    public static function getRedisPort()
    {
        return defined('WP_REDIS_PORT') ? WP_REDIS_PORT : 6379;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_PASSWORD
     * 
     * @return string
     */
    public static function getRedisPassword()
    {
        return defined('WP_REDIS_PASSWORD') ? WP_REDIS_PASSWORD : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_DATABASE
     * 
     * @return int
     */
    public static function getRedisDatabase()
    {
        return defined('WP_REDIS_DATABASE') ? WP_REDIS_DATABASE : 0;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_TIMEOUT
     * 
     * @return int
     */
    public static function getRedisTimeout()
    {
        return defined('WP_REDIS_TIMEOUT') ? WP_REDIS_TIMEOUT : 1;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_READ_TIMEOUT
     * 
     * @return int
     */
    public static function getRedisReadTimeout()
    {
        return defined('WP_REDIS_READ_TIMEOUT') ? WP_REDIS_READ_TIMEOUT : 1;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_RETRY_INTERVAL
     * 
     * @return int
     */
    public static function getRedisRetryInterval()
    {
        return defined('WP_REDIS_RETRY_INTERVAL') ? WP_REDIS_RETRY_INTERVAL : 100;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_MAXTTL
     * 
     * @return int
     */
    public static function getRedisTtl()
    {
        return defined('WP_REDIS_MAXTTL') ? WP_REDIS_MAXTTL : 86400;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_PREFIX
     * 
     * @return string
     */
    public static function getRedisPrefix()
    {
        return defined('WP_REDIS_PREFIX') ? WP_REDIS_PREFIX : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_SELECTIVE_FLUSH
     * 
     * @return bool
     */
    public static function isRedisSelectiveFlush()
    {
        return defined('WP_REDIS_SELECTIVE_FLUSH') && WP_REDIS_SELECTIVE_FLUSH;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_IGBINARY
     * 
     * @return bool
     */
    public static function isRedisIgbinary()
    {
        return defined('WP_REDIS_IGBINARY') && WP_REDIS_IGBINARY;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_SERIALIZER
     * 
     * @return string
     */
    public static function getRedisSerializer()
    {
        return defined('WP_REDIS_SERIALIZER') ? WP_REDIS_SERIALIZER : 'php';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_DISABLED
     * 
     * @return bool
     */
    public static function isRedisDisabled()
    {
        return defined('WP_REDIS_DISABLED') && WP_REDIS_DISABLED;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_CLIENT
     * 
     * @return string
     */
    public static function getRedisClient()
    {
        return defined('WP_REDIS_CLIENT') ? WP_REDIS_CLIENT : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_CLUSTER
     * 
     * @return array
     */
    public static function getRedisCluster()
    {
        return defined('WP_REDIS_CLUSTER') ? WP_REDIS_CLUSTER : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_SERVERS
     * 
     * @return array
     */
    public static function getRedisServers()
    {
        return defined('WP_REDIS_SERVERS') ? WP_REDIS_SERVERS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_SENTINEL
     * 
     * @return string
     */
    public static function getRedisSentinel()
    {
        return defined('WP_REDIS_SENTINEL') ? WP_REDIS_SENTINEL : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_SENTINEL_SERVERS
     * 
     * @return array
     */
    public static function getRedisSentinelServers()
    {
        return defined('WP_REDIS_SENTINEL_SERVERS') ? WP_REDIS_SENTINEL_SERVERS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_SHARDS
     * 
     * @return array
     */
    public static function getRedisShards()
    {
        return defined('WP_REDIS_SHARDS') ? WP_REDIS_SHARDS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_SHARD_STRATEGY
     * 
     * @return string
     */
    public static function getRedisShardStrategy()
    {
        return defined('WP_REDIS_SHARD_STRATEGY') ? WP_REDIS_SHARD_STRATEGY : 'standard';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_GLOBAL_GROUPS
     * 
     * @return array
     */
    public static function getRedisGlobalGroups()
    {
        return defined('WP_REDIS_GLOBAL_GROUPS') ? WP_REDIS_GLOBAL_GROUPS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_IGNORED_GROUPS
     * 
     * @return array
     */
    public static function getRedisIgnoredGroups()
    {
        return defined('WP_REDIS_IGNORED_GROUPS') ? WP_REDIS_IGNORED_GROUPS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_UNFLUSHABLE_GROUPS
     * 
     * @return array
     */
    public static function getRedisUnflushableGroups()
    {
        return defined('WP_REDIS_UNFLUSHABLE_GROUPS') ? WP_REDIS_UNFLUSHABLE_GROUPS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS
     * 
     * @return bool
     */
    public static function isRedisMetrics()
    {
        return defined('WP_REDIS_METRICS') && WP_REDIS_METRICS;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_SAMPLE_RATE
     * 
     * @return int
     */
    public static function getRedisMetricsSampleRate()
    {
        return defined('WP_REDIS_METRICS_SAMPLE_RATE') ? WP_REDIS_METRICS_SAMPLE_RATE : 1;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_MAX_TIME
     * 
     * @return int
     */
    public static function getRedisMetricsMaxTime()
    {
        return defined('WP_REDIS_METRICS_MAX_TIME') ? WP_REDIS_METRICS_MAX_TIME : 60;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_THRESHOLD
     * 
     * @return int
     */
    public static function getRedisMetricsThreshold()
    {
        return defined('WP_REDIS_METRICS_THRESHOLD') ? WP_REDIS_METRICS_THRESHOLD : 1000;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR
     * 
     * @return string
     */
    public static function getRedisMetricsCollector()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR') ? WP_REDIS_METRICS_COLLECTOR : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_HOST
     * 
     * @return string
     */
    public static function getRedisMetricsCollectorHost()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_HOST') ? WP_REDIS_METRICS_COLLECTOR_HOST : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_PORT
     * 
     * @return int
     */
    public static function getRedisMetricsCollectorPort()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_PORT') ? WP_REDIS_METRICS_COLLECTOR_PORT : 0;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_AUTH
     * 
     * @return string
     */
    public static function getRedisMetricsCollectorAuth()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_AUTH') ? WP_REDIS_METRICS_COLLECTOR_AUTH : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_TIMEOUT
     * 
     * @return int
     */
    public static function getRedisMetricsCollectorTimeout()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_TIMEOUT') ? WP_REDIS_METRICS_COLLECTOR_TIMEOUT : 1;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_READ_TIMEOUT
     * 
     * @return int
     */
    public static function getRedisMetricsCollectorReadTimeout()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_READ_TIMEOUT') ? WP_REDIS_METRICS_COLLECTOR_READ_TIMEOUT : 1;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_RETRY_INTERVAL
     * 
     * @return int
     */
    public static function getRedisMetricsCollectorRetryInterval()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_RETRY_INTERVAL') ? WP_REDIS_METRICS_COLLECTOR_RETRY_INTERVAL : 100;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_MAXTTL
     * 
     * @return int
     */
    public static function getRedisMetricsCollectorTtl()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_MAXTTL') ? WP_REDIS_METRICS_COLLECTOR_MAXTTL : 86400;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_PREFIX
     * 
     * @return string
     */
    public static function getRedisMetricsCollectorPrefix()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_PREFIX') ? WP_REDIS_METRICS_COLLECTOR_PREFIX : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_SELECTIVE_FLUSH
     * 
     * @return bool
     */
    public static function isRedisMetricsCollectorSelectiveFlush()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_SELECTIVE_FLUSH') && WP_REDIS_METRICS_COLLECTOR_SELECTIVE_FLUSH;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_IGBINARY
     * 
     * @return bool
     */
    public static function isRedisMetricsCollectorIgbinary()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_IGBINARY') && WP_REDIS_METRICS_COLLECTOR_IGBINARY;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_SERIALIZER
     * 
     * @return string
     */
    public static function getRedisMetricsCollectorSerializer()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_SERIALIZER') ? WP_REDIS_METRICS_COLLECTOR_SERIALIZER : 'php';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_DISABLED
     * 
     * @return bool
     */
    public static function isRedisMetricsCollectorDisabled()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_DISABLED') && WP_REDIS_METRICS_COLLECTOR_DISABLED;
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_CLIENT
     * 
     * @return string
     */
    public static function getRedisMetricsCollectorClient()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_CLIENT') ? WP_REDIS_METRICS_COLLECTOR_CLIENT : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_CLUSTER
     * 
     * @return array
     */
    public static function getRedisMetricsCollectorCluster()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_CLUSTER') ? WP_REDIS_METRICS_COLLECTOR_CLUSTER : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_SERVERS
     * 
     * @return array
     */
    public static function getRedisMetricsCollectorServers()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_SERVERS') ? WP_REDIS_METRICS_COLLECTOR_SERVERS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_SENTINEL
     * 
     * @return string
     */
    public static function getRedisMetricsCollectorSentinel()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_SENTINEL') ? WP_REDIS_METRICS_COLLECTOR_SENTINEL : '';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_SENTINEL_SERVERS
     * 
     * @return array
     */
    public static function getRedisMetricsCollectorSentinelServers()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_SENTINEL_SERVERS') ? WP_REDIS_METRICS_COLLECTOR_SENTINEL_SERVERS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_SHARDS
     * 
     * @return array
     */
    public static function getRedisMetricsCollectorShards()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_SHARDS') ? WP_REDIS_METRICS_COLLECTOR_SHARDS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_SHARD_STRATEGY
     * 
     * @return string
     */
    public static function getRedisMetricsCollectorShardStrategy()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_SHARD_STRATEGY') ? WP_REDIS_METRICS_COLLECTOR_SHARD_STRATEGY : 'standard';
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_GLOBAL_GROUPS
     * 
     * @return array
     */
    public static function getRedisMetricsCollectorGlobalGroups()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_GLOBAL_GROUPS') ? WP_REDIS_METRICS_COLLECTOR_GLOBAL_GROUPS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_IGNORED_GROUPS
     * 
     * @return array
     */
    public static function getRedisMetricsCollectorIgnoredGroups()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_IGNORED_GROUPS') ? WP_REDIS_METRICS_COLLECTOR_IGNORED_GROUPS : [];
    }
    
    /**
     * Verifica se a página atual é uma página de WP_REDIS_METRICS_COLLECTOR_UNFLUSHABLE_GROUPS
     * 
     * @return array
     */
    public static function getRedisMetricsCollectorUnflushableGroups()
    {
        return defined('WP_REDIS_METRICS_COLLECTOR_UNFLUSHABLE_GROUPS') ? WP_REDIS_METRICS_COLLECTOR_UNFLUSHABLE_GROUPS : [];
    }
}
