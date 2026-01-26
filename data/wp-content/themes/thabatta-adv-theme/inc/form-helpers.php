<?php
/**
 * Helpers compartilhados para formulários do tema Thabatta Advocacia
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Obtém o e-mail de notificação configurado via ACF (option) ou fallback no admin.
 *
 * @param string $acf_option_key Chave do campo ACF em options.
 * @return string
 */
function thabatta_get_notification_email($acf_option_key) {
    $admin_email = get_option('admin_email');

    if (function_exists('get_field')) {
        $notification_email = get_field($acf_option_key, 'option');
        if (!empty($notification_email)) {
            $admin_email = $notification_email;
        }
    }

    return $admin_email;
}

/**
 * Normaliza o payload do formulário serializado.
 *
 * @param string|null $form_data
 * @return array
 */
function thabatta_parse_form_data($form_data) {
    $parsed = array();

    if (is_string($form_data)) {
        parse_str(wp_unslash($form_data), $parsed);
    }

    return $parsed;
}

/**
 * Função auxiliar para obter IP do cliente.
 *
 * @return string
 */
function thabatta_get_client_ip() {
    $ip_keys = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    );

    foreach ($ip_keys as $key) {
        if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
            return sanitize_text_field($_SERVER[$key]);
        }
    }

    return '127.0.0.1'; // Fallback para localhost
}
