<?php
/**
 * Funções para lidar com o formulário de contato simples
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Handler AJAX para o formulário de contato
 */
function thabatta_handle_contact_form() {
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_contact_nonce')) {
        wp_send_json_error(array('message' => esc_html__('Verificação de segurança falhou. Por favor, tente novamente.', 'thabatta-adv')));
        wp_die();
    }
    
    // Recuperar e sanitizar dados
    parse_str($_POST['formData'], $form_data);
    
    $name = isset($form_data['contact_name']) ? sanitize_text_field($form_data['contact_name']) : '';
    $email = isset($form_data['contact_email']) ? sanitize_email($form_data['contact_email']) : '';
    $phone = isset($form_data['contact_phone']) ? sanitize_text_field($form_data['contact_phone']) : '';
    $subject = isset($form_data['contact_subject']) ? sanitize_text_field($form_data['contact_subject']) : '';
    $message = isset($form_data['contact_message']) ? sanitize_textarea_field($form_data['contact_message']) : '';
    $privacy = isset($form_data['contact_privacy']) ? (bool) $form_data['contact_privacy'] : false;
    
    // Validar campos obrigatórios
    if (empty($name) || empty($email) || empty($subject) || empty($message) || !$privacy) {
        wp_send_json_error(array('message' => esc_html__('Por favor, preencha todos os campos obrigatórios.', 'thabatta-adv')));
        wp_die();
    }
    
    // Validar email
    if (!is_email($email)) {
        wp_send_json_error(array('message' => esc_html__('Por favor, insira um endereço de e-mail válido.', 'thabatta-adv')));
        wp_die();
    }
    
    // Enviar e-mail
    $result = thabatta_send_contact_email($name, $email, $phone, $subject, $message);
    
    if ($result) {
        wp_send_json_success(array('message' => esc_html__('Mensagem enviada com sucesso! Entraremos em contato em breve.', 'thabatta-adv')));
    } else {
        wp_send_json_error(array('message' => esc_html__('Ocorreu um erro ao enviar sua mensagem. Por favor, tente novamente.', 'thabatta-adv')));
    }
    wp_die();
}
add_action('wp_ajax_thabatta_contact', 'thabatta_handle_contact_form');
add_action('wp_ajax_nopriv_thabatta_contact', 'thabatta_handle_contact_form');

/**
 * Envia e-mail do formulário de contato
 */
function thabatta_send_contact_email($name, $email, $phone, $subject, $message) {
    // Obter destinatário do e-mail (Opções ACF)
    $admin_email = get_option('admin_email');
    
    if (function_exists('get_field')) {
        $notification_email = get_field('email_notificacao_contato', 'option');
        
        if (!empty($notification_email)) {
            $admin_email = $notification_email;
        }
    }
    
    // Formatar mensagem
    $email_subject = '[Thabatta Advocacia] ' . $subject;
    
    $email_message = sprintf(esc_html__('Você recebeu uma nova mensagem do formulário de contato.', 'thabatta-adv')) . "\n\n";
    $email_message .= sprintf(esc_html__('Nome: %s', 'thabatta-adv'), $name) . "\n";
    $email_message .= sprintf(esc_html__('E-mail: %s', 'thabatta-adv'), $email) . "\n";
    
    if (!empty($phone)) {
        $email_message .= sprintf(esc_html__('Telefone: %s', 'thabatta-adv'), $phone) . "\n";
    }
    
    $email_message .= sprintf(esc_html__('Assunto: %s', 'thabatta-adv'), $subject) . "\n\n";
    $email_message .= sprintf(esc_html__('Mensagem:', 'thabatta-adv')) . "\n";
    $email_message .= $message . "\n\n";
    
    // Adicionar dados técnicos
    $email_message .= sprintf(esc_html__('--- Informações Técnicas ---', 'thabatta-adv')) . "\n";
    $email_message .= sprintf(esc_html__('IP: %s', 'thabatta-adv'), thabatta_get_client_ip()) . "\n";
    $email_message .= sprintf(esc_html__('Data e Hora: %s', 'thabatta-adv'), current_time('d/m/Y H:i:s')) . "\n";
    $email_message .= sprintf(esc_html__('Página de Origem: %s', 'thabatta-adv'), wp_get_referer()) . "\n";
    
    // Configurar cabeçalhos
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $name . ' <' . $email . '>',
        'Reply-To: ' . $email,
    );
    
    // Enviar e-mail
    return wp_mail($admin_email, $email_subject, $email_message, $headers);
}

/**
 * Função auxiliar para obter IP do cliente
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