<?php
/**
 * Manipuladores AJAX para o tema Thabatta Advocacia
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Processa o envio do formulário de consulta
 */
function thabatta_submit_consultation() {
    // Verificar nonce para segurança
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'thabatta_consultation_nonce')) {
        wp_send_json_error(array(
            'message' => __('Falha na verificação de segurança. Por favor, atualize a página e tente novamente.', 'thabatta-adv')
        ));
        return;
    }
    
    // Sanitizar dados recebidos
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $cpf_cnpj = isset($_POST['cpfcnpj']) ? sanitize_text_field($_POST['cpfcnpj']) : '';
    $law_area = isset($_POST['area']) ? sanitize_text_field($_POST['area']) : '';
    $urgency = isset($_POST['urgency']) ? sanitize_text_field($_POST['urgency']) : 'media';
    $case_details = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    $contact_preference = isset($_POST['contact_preference']) ? sanitize_text_field($_POST['contact_preference']) : 'phone';
    $confirmation = isset($_POST['confirmation']) && $_POST['confirmation'] === 'on';
    
    // Verificar campos obrigatórios
    if (empty($name) || empty($email) || empty($phone) || empty($law_area) || !$confirmation) {
        wp_send_json_error(array(
            'message' => __('Por favor, preencha todos os campos obrigatórios.', 'thabatta-adv')
        ));
        return;
    }
    
    // Validar formato de e-mail
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Por favor, forneça um endereço de e-mail válido.', 'thabatta-adv')
        ));
        return;
    }
    
    // Criar post personalizado para a consulta
    $post_data = array(
        'post_title'    => sprintf(__('Consulta: %s (%s)', 'thabatta-adv'), $name, date_i18n('d/m/Y H:i')),
        'post_content'  => $case_details,
        'post_status'   => 'private', // Alterado para private para manter os dados seguros
        'post_type'     => 'consultation',
    );
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        wp_send_json_error(array(
            'message' => __('Erro ao registrar sua consulta. Por favor, tente novamente.', 'thabatta-adv')
        ));
        return;
    }
    
    // Adicionar metadados
    update_post_meta($post_id, '_consultation_name', $name);
    update_post_meta($post_id, '_consultation_email', $email);
    update_post_meta($post_id, '_consultation_phone', $phone);
    update_post_meta($post_id, '_consultation_cpf_cnpj', $cpf_cnpj);
    update_post_meta($post_id, '_consultation_area', $law_area);
    update_post_meta($post_id, '_consultation_urgency', $urgency);
    update_post_meta($post_id, '_consultation_contact_preference', $contact_preference);
    update_post_meta($post_id, '_consultation_date', current_time('mysql'));
    update_post_meta($post_id, '_consultation_status', 'new');
    
    // Enviar e-mail de notificação para o administrador
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $subject = sprintf(__('[%s] Nova solicitação de consulta', 'thabatta-adv'), $site_name);
    
    // Formatar níveis de urgência
    $urgency_labels = array(
        'baixa' => __('Baixa - Consulta informativa', 'thabatta-adv'),
        'media' => __('Média - Preciso resolver nas próximas semanas', 'thabatta-adv'),
        'alta' => __('Alta - Tenho prazos críticos', 'thabatta-adv')
    );
    $urgency_text = isset($urgency_labels[$urgency]) ? $urgency_labels[$urgency] : $urgency;
    
    // Formatar preferência de contato
    $contact_labels = array(
        'phone' => __('Telefone', 'thabatta-adv'),
        'email' => __('E-mail', 'thabatta-adv'),
        'whatsapp' => __('WhatsApp', 'thabatta-adv')
    );
    $contact_text = isset($contact_labels[$contact_preference]) ? $contact_labels[$contact_preference] : $contact_preference;
    
    // Montar corpo do e-mail
    $message = sprintf(__('Nova solicitação de consulta recebida de %s', 'thabatta-adv'), $name) . "\n\n";
    $message .= __('Detalhes da solicitação:', 'thabatta-adv') . "\n";
    $message .= __('Nome: ', 'thabatta-adv') . $name . "\n";
    $message .= __('E-mail: ', 'thabatta-adv') . $email . "\n";
    $message .= __('Telefone: ', 'thabatta-adv') . $phone . "\n";
    $message .= __('CPF/CNPJ: ', 'thabatta-adv') . $cpf_cnpj . "\n";
    $message .= __('Área de Atuação: ', 'thabatta-adv') . $law_area . "\n";
    $message .= __('Urgência: ', 'thabatta-adv') . $urgency_text . "\n";
    $message .= __('Forma de Contato Preferida: ', 'thabatta-adv') . $contact_text . "\n\n";
    
    if (!empty($case_details)) {
        $message .= __('Detalhes do Caso:', 'thabatta-adv') . "\n" . $case_details . "\n\n";
    }
    
    $message .= __('Para gerenciar esta consulta, acesse o painel administrativo:', 'thabatta-adv') . "\n";
    $message .= admin_url('post.php?post=' . $post_id . '&action=edit');
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    // Enviar e-mail
    $mail_sent = wp_mail($admin_email, $subject, $message, $headers);
    
    // Enviar e-mail de confirmação para o cliente
    $client_subject = sprintf(__('Sua solicitação de consulta foi recebida - %s', 'thabatta-adv'), $site_name);
    
    $client_message = sprintf(__('Olá %s,', 'thabatta-adv'), $name) . "\n\n";
    $client_message .= __('Recebemos sua solicitação de consulta e agradecemos pelo seu interesse.', 'thabatta-adv') . "\n\n";
    $client_message .= __('Entraremos em contato em breve para agendar sua consulta.', 'thabatta-adv') . "\n\n";
    $client_message .= __('Resumo da sua solicitação:', 'thabatta-adv') . "\n";
    $client_message .= __('Área de Atuação: ', 'thabatta-adv') . $law_area . "\n";
    $client_message .= __('Urgência: ', 'thabatta-adv') . $urgency_text . "\n";
    $client_message .= __('Forma de Contato Preferida: ', 'thabatta-adv') . $contact_text . "\n\n";
    $client_message .= __('Atenciosamente,', 'thabatta-adv') . "\n";
    $client_message .= $site_name;
    
    wp_mail($email, $client_subject, $client_message, $headers);
    
    // Retornar sucesso
    wp_send_json_success(array(
        'message' => __('Sua solicitação de consulta foi enviada com sucesso. Entraremos em contato em breve.', 'thabatta-adv')
    ));
}
add_action('wp_ajax_thabatta_submit_consultation', 'thabatta_submit_consultation');
add_action('wp_ajax_nopriv_thabatta_submit_consultation', 'thabatta_submit_consultation');

/**
 * Registra o tipo de post personalizado para consultas
 */
function thabatta_register_consultation_post_type() {
    $labels = array(
        'name'               => _x('Consultas', 'post type general name', 'thabatta-adv'),
        'singular_name'      => _x('Consulta', 'post type singular name', 'thabatta-adv'),
        'menu_name'          => _x('Consultas', 'admin menu', 'thabatta-adv'),
        'name_admin_bar'     => _x('Consulta', 'add new on admin bar', 'thabatta-adv'),
        'add_new'            => _x('Adicionar Nova', 'consultation', 'thabatta-adv'),
        'add_new_item'       => __('Adicionar Nova Consulta', 'thabatta-adv'),
        'new_item'           => __('Nova Consulta', 'thabatta-adv'),
        'edit_item'          => __('Editar Consulta', 'thabatta-adv'),
        'view_item'          => __('Ver Consulta', 'thabatta-adv'),
        'all_items'          => __('Todas as Consultas', 'thabatta-adv'),
        'search_items'       => __('Pesquisar Consultas', 'thabatta-adv'),
        'parent_item_colon'  => __('Consultas Pai:', 'thabatta-adv'),
        'not_found'          => __('Nenhuma consulta encontrada.', 'thabatta-adv'),
        'not_found_in_trash' => __('Nenhuma consulta encontrada na lixeira.', 'thabatta-adv')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Consultas solicitadas pelos clientes', 'thabatta-adv'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'consultation'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => array('title', 'editor', 'custom-fields')
    );

    register_post_type('consultation', $args);
}
add_action('init', 'thabatta_register_consultation_post_type');

/**
 * Adiciona meta box para os detalhes da consulta
 */
function thabatta_add_consultation_meta_boxes() {
    add_meta_box(
        'consultation_details',
        __('Detalhes da Consulta', 'thabatta-adv'),
        'thabatta_consultation_details_callback',
        'consultation',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'thabatta_add_consultation_meta_boxes');

/**
 * Callback para a meta box de detalhes da consulta
 */
function thabatta_consultation_details_callback($post) {
    // Buscar metadados
    $name = get_post_meta($post->ID, '_consultation_name', true);
    $email = get_post_meta($post->ID, '_consultation_email', true);
    $phone = get_post_meta($post->ID, '_consultation_phone', true);
    $cpf_cnpj = get_post_meta($post->ID, '_consultation_cpf_cnpj', true);
    $area = get_post_meta($post->ID, '_consultation_area', true);
    $urgency = get_post_meta($post->ID, '_consultation_urgency', true);
    $contact_preference = get_post_meta($post->ID, '_consultation_contact_preference', true);
    $date = get_post_meta($post->ID, '_consultation_date', true);
    $status = get_post_meta($post->ID, '_consultation_status', true);
    
    // Formatar níveis de urgência
    $urgency_labels = array(
        'baixa' => __('Baixa - Consulta informativa', 'thabatta-adv'),
        'media' => __('Média - Preciso resolver nas próximas semanas', 'thabatta-adv'),
        'alta' => __('Alta - Tenho prazos críticos', 'thabatta-adv')
    );
    $urgency_text = isset($urgency_labels[$urgency]) ? $urgency_labels[$urgency] : $urgency;
    
    // Formatar preferência de contato
    $contact_labels = array(
        'phone' => __('Telefone', 'thabatta-adv'),
        'email' => __('E-mail', 'thabatta-adv'),
        'whatsapp' => __('WhatsApp', 'thabatta-adv')
    );
    $contact_text = isset($contact_labels[$contact_preference]) ? $contact_labels[$contact_preference] : $contact_preference;
    
    // Obter nome da área de atuação
    $area_name = '';
    if (is_numeric($area)) {
        $area_post = get_post($area);
        if ($area_post) {
            $area_name = $area_post->post_title;
        }
    } else {
        // Se não for numérico, usamos os valores padrão
        $default_areas = array(
            'civil' => __('Direito Civil', 'thabatta-adv'),
            'empresarial' => __('Direito Empresarial', 'thabatta-adv'),
            'trabalhista' => __('Direito Trabalhista', 'thabatta-adv'),
            'consumidor' => __('Direito do Consumidor', 'thabatta-adv'),
            'previdenciario' => __('Direito Previdenciário', 'thabatta-adv'),
            'outro' => __('Outro', 'thabatta-adv')
        );
        $area_name = isset($default_areas[$area]) ? $default_areas[$area] : $area;
    }
    
    // Formatar status
    $status_labels = array(
        'new' => __('Nova', 'thabatta-adv'),
        'contacted' => __('Cliente Contatado', 'thabatta-adv'),
        'scheduled' => __('Consulta Agendada', 'thabatta-adv'),
        'done' => __('Consulta Realizada', 'thabatta-adv'),
        'canceled' => __('Cancelada', 'thabatta-adv')
    );
    $status_text = isset($status_labels[$status]) ? $status_labels[$status] : __('Nova', 'thabatta-adv');
    
    // Adicionar nonce para segurança
    wp_nonce_field('thabatta_consultation_details', 'thabatta_consultation_details_nonce');
    
    // Exibir formulário
    ?>
    <style>
        .consultation-details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .consultation-details-table th {
            text-align: left;
            width: 200px;
            padding: 8px;
            background-color: #f5f5f5;
        }
        .consultation-details-table td {
            padding: 8px;
        }
        .consultation-status-select {
            padding: 5px;
            width: 100%;
            max-width: 300px;
        }
    </style>
    
    <table class="consultation-details-table">
        <tr>
            <th><?php _e('Nome', 'thabatta-adv'); ?>:</th>
            <td><?php echo esc_html($name); ?></td>
        </tr>
        <tr>
            <th><?php _e('E-mail', 'thabatta-adv'); ?>:</th>
            <td><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></td>
        </tr>
        <tr>
            <th><?php _e('Telefone', 'thabatta-adv'); ?>:</th>
            <td>
                <?php echo esc_html($phone); ?>
                <?php if ($contact_preference === 'whatsapp'): ?>
                    <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $phone); ?>" target="_blank" class="button button-small">
                        <?php _e('WhatsApp', 'thabatta-adv'); ?>
                    </a>
                <?php else: ?>
                    <a href="tel:<?php echo preg_replace('/\D/', '', $phone); ?>" class="button button-small">
                        <?php _e('Ligar', 'thabatta-adv'); ?>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?php _e('CPF/CNPJ', 'thabatta-adv'); ?>:</th>
            <td><?php echo esc_html($cpf_cnpj); ?></td>
        </tr>
        <tr>
            <th><?php _e('Área de Atuação', 'thabatta-adv'); ?>:</th>
            <td><?php echo esc_html($area_name); ?></td>
        </tr>
        <tr>
            <th><?php _e('Urgência', 'thabatta-adv'); ?>:</th>
            <td><?php echo esc_html($urgency_text); ?></td>
        </tr>
        <tr>
            <th><?php _e('Forma de Contato Preferida', 'thabatta-adv'); ?>:</th>
            <td><?php echo esc_html($contact_text); ?></td>
        </tr>
        <tr>
            <th><?php _e('Data da Solicitação', 'thabatta-adv'); ?>:</th>
            <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($date))); ?></td>
        </tr>
        <tr>
            <th><?php _e('Status', 'thabatta-adv'); ?>:</th>
            <td>
                <select name="consultation_status" class="consultation-status-select">
                    <?php foreach ($status_labels as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($status, $value); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Salva os metadados da consulta
 */
function thabatta_save_consultation_details($post_id) {
    // Verificar se é um salvamento automático
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Verificar o nonce
    if (!isset($_POST['thabatta_consultation_details_nonce']) || 
        !wp_verify_nonce($_POST['thabatta_consultation_details_nonce'], 'thabatta_consultation_details')) {
        return;
    }
    
    // Verificar permissões
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Salvar status
    if (isset($_POST['consultation_status'])) {
        update_post_meta(
            $post_id,
            '_consultation_status',
            sanitize_text_field($_POST['consultation_status'])
        );
    }
}
add_action('save_post_consultation', 'thabatta_save_consultation_details'); 