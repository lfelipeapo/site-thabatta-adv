<?php
define('WP_CACHE', true); // Boost Cache Plugin
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * This has been slightly modified (to read environment variables) for use in Docker.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// IMPORTANT: this file needs to stay in-sync with https://github.com/WordPress/WordPress/blob/master/wp-config-sample.php
// (it gets parsed by the upstream wizard in https://github.com/WordPress/WordPress/blob/f27cb65e1ef25d11b535695a660e7282b98eb742/wp-admin/setup-config.php#L356-L392)

// a helper function to lookup "env_FILE", "env", then fallback
if (!function_exists('getenv_docker')) {
    // https://github.com/docker-library/wordpress/issues/588 (WP-CLI will load this file 2x)
    function getenv_docker($env, $default)
    {
        if ($fileEnv = getenv($env . '_FILE')) {
            return rtrim(file_get_contents($fileEnv), "\r\n");
        } else if (($val = getenv($env)) !== false) {
            return $val;
        } else {
            return $default;
        }
    }
}

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv_docker('WORDPRESS_DB_NAME', 'wordpress'));

/** Database username */
define('DB_USER', getenv_docker('WORDPRESS_DB_USER', 'example username'));

/** Database password */
define('DB_PASSWORD', getenv_docker('WORDPRESS_DB_PASSWORD', 'example password'));

/**
 * Docker image fallback values above are sourced from the official WordPress installation wizard:
 * https://github.com/WordPress/WordPress/blob/1356f6537220ffdc32b9dad2a6cdbe2d010b7a88/wp-admin/setup-config.php#L224-L238
 * (However, using "example username" and "example password" in your database is strongly discouraged.  Please use strong, random credentials!)
 */

/** Database hostname */
define('DB_HOST', getenv_docker('WORDPRESS_DB_HOST', 'db'));

/** Database charset to use in creating database tables. */
define('DB_CHARSET', getenv_docker('WORDPRESS_DB_CHARSET', 'utf8'));

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', getenv_docker('WORDPRESS_DB_COLLATE', ''));

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', getenv_docker('WORDPRESS_AUTH_KEY', '65966234ecdb31a4073cf13008edcc61b3987fb9'));
define('SECURE_AUTH_KEY', getenv_docker('WORDPRESS_SECURE_AUTH_KEY', 'd45b077c8e189ed39227ad3b589b0cd4e2f9bcdb'));
define('LOGGED_IN_KEY', getenv_docker('WORDPRESS_LOGGED_IN_KEY', '6d8537ca9fe3c9a92e25275d5a498ca68e82d139'));
define('NONCE_KEY', getenv_docker('WORDPRESS_NONCE_KEY', 'ddb14e1f9b72a6bf373facbe9df978f64998345f'));
define('AUTH_SALT', getenv_docker('WORDPRESS_AUTH_SALT', '9b9f0ff5347dd5ae074b7e94fd22a442bc0ba671'));
define('SECURE_AUTH_SALT', getenv_docker('WORDPRESS_SECURE_AUTH_SALT', '90b491de33a1c8d968c21dd7b962b4125a3301c5'));
define('LOGGED_IN_SALT', getenv_docker('WORDPRESS_LOGGED_IN_SALT', 'b2df7cc3aa3b0fea757d6947a30e7ffb0133fabb'));
define('NONCE_SALT', getenv_docker('WORDPRESS_NONCE_SALT', '002839996e41e470504aacd86d46cc9854405498'));
// (See also https://wordpress.stackexchange.com/a/152905/199287)

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv_docker('WORDPRESS_TABLE_PREFIX', 'wp_');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);


// No wp-config.php
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'localhost');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/* Add any custom values between this line and the "stop editing" line. */
define('DISABLE_WP_CRON', true);

// If we're behind a proxy server and using HTTPS, we need to alert WordPress of that fact
// see also https://wordpress.org/support/article/administration-over-ssl/#using-a-reverse-proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
    $_SERVER['HTTPS'] = 'on';
}
// (we include this by default because reverse proxying is extremely common in container environments)

if ($configExtra = getenv_docker('WORDPRESS_CONFIG_EXTRA', '')) {
    eval($configExtra);
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Configurações de memória e tempo limite
@ini_set('memory_limit', '512M');
if (!ini_get('max_execution_time') || ini_get('max_execution_time') < 300) {
    set_time_limit(300);
}

// Suprimir avisos específicos
if (!function_exists('suppress_jwt_auth_notice')) {
    function suppress_jwt_auth_notice() {
        remove_action('admin_notices', array('JWT_AUTH_Public', 'admin_notices'));
    }
}

// Adiciona o hook somente se a função add_action existir (não em WP-CLI)
if (function_exists('add_action') && !defined('WP_CLI')) {
    add_action('init', 'suppress_jwt_auth_notice', 1);
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

//Debugs configs
// define('WP_DEBUG', false);
// define('WP_DEBUG_LOG', true);
// define('WP_DEBUG_DISPLAY', false);
// define('FS_METHOD', 'ftpext');
define('FTP_HOST', '127.0.0.1:2222'); // Porta configurada para o SFTP
define('FTP_USER', 'wp-user'); // Usuário configurado no serviço SFTP
define('FTP_PASS', 'password'); // Senha configurada no serviço SFTP
define('FTP_BASE', '/home/wp-user/data'); // Diretório raiz do SFTP
define('FTP_CONTENT_DIR', '/home/wp-user/data/wp-content'); // Diretório wp-content
define('FTP_PLUGIN_DIR', '/home/wp-user/data/wp-content/plugins'); // Diretório de plugins

@ini_set('display_errors', 1);
@ini_set('error_reporting', E_ALL & ~E_DEPRECATED & ~E_NOTICE);
set_time_limit(120);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
