<?php
/**
 * Funções para lidar com o formulário de consulta
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

/**
 * Registrar CPT Lead para armazenar consultas do formulário
 */
function thabatta_register_lead_post_type() {
    $labels = array(
        'name'               => esc_html__('Leads', 'thabatta-adv'),
        'singular_name'      => esc_html__('Lead', 'thabatta-adv'),
        'add_new'            => esc_html__('Adicionar Novo', 'thabatta-adv'),
        'add_new_item'       => esc_html__('Adicionar Novo Lead', 'thabatta-adv'),
        'edit_item'          => esc_html__('Editar Lead', 'thabatta-adv'),
        'new_item'           => esc_html__('Novo Lead', 'thabatta-adv'),
        'view_item'          => esc_html__('Ver Lead', 'thabatta-adv'),
        'search_items'       => esc_html__('Buscar Leads', 'thabatta-adv'),
        'not_found'          => esc_html__('Nenhum lead encontrado', 'thabatta-adv'),
        'not_found_in_trash' => esc_html__('Nenhum lead encontrado na lixeira', 'thabatta-adv'),
        'menu_name'          => esc_html__('Leads', 'thabatta-adv'),
    );
    
    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 30,
        'menu_icon'           => 'dashicons-email',
        'supports'            => array('title', 'editor'),
    );
    
    register_post_type('lead', $args);
}
add_action('init', 'thabatta_register_lead_post_type');

/**
 * Adicionar colunas personalizadas ao CPT Lead
 */
function thabatta_lead_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = esc_html__('Nome', 'thabatta-adv');
        } elseif ($key === 'date') {
            $new_columns['phone'] = esc_html__('Telefone', 'thabatta-adv');
            $new_columns['cpf_cnpj'] = esc_html__('CPF/CNPJ', 'thabatta-adv');
            $new_columns['area'] = esc_html__('Área', 'thabatta-adv');
            $new_columns[$key] = $value;
        } else {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
add_filter('manage_lead_posts_columns', 'thabatta_lead_columns');

/**
 * Preencher colunas personalizadas do CPT Lead
 */
function thabatta_lead_custom_column($column, $post_id) {
    switch ($column) {
        case 'phone':
            echo esc_html(get_post_meta($post_id, '_phone', true));
            break;
        
        case 'cpf_cnpj':
            echo esc_html(get_post_meta($post_id, '_cpf_cnpj', true));
            break;
        
        case 'area':
            echo esc_html(get_post_meta($post_id, '_area', true));
            break;
    }
}
add_action('manage_lead_posts_custom_column', 'thabatta_lead_custom_column', 10, 2);

/**
 * Adicionar metabox para os leads
 */
function thabatta_add_lead_meta_boxes() {
    add_meta_box(
        'thabatta_lead_details',
        esc_html__('Detalhes do Lead', 'thabatta-adv'),
        'thabatta_lead_details_callback',
        'lead',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'thabatta_add_lead_meta_boxes');

/**
 * Callback da metabox de detalhes do lead
 */
function thabatta_lead_details_callback($post) {
    // Recuperar valores
    $phone = get_post_meta($post->ID, '_phone', true);
    $cpf_cnpj = get_post_meta($post->ID, '_cpf_cnpj', true);
    $area = get_post_meta($post->ID, '_area', true);
    
    // Campo nonce de segurança
    wp_nonce_field('thabatta_lead_details', 'thabatta_lead_details_nonce');
    
    // Exibir campos
    ?>
    <table class="form-table">
        <tr>
            <th><label for="phone"><?php esc_html_e('Telefone:', 'thabatta-adv'); ?></label></th>
            <td><input type="text" id="phone" name="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
        </tr>
        
        <tr>
            <th><label for="cpf_cnpj"><?php esc_html_e('CPF/CNPJ:', 'thabatta-adv'); ?></label></th>
            <td><input type="text" id="cpf_cnpj" name="cpf_cnpj" value="<?php echo esc_attr($cpf_cnpj); ?>" class="regular-text"></td>
        </tr>
        
        <tr>
            <th><label for="area"><?php esc_html_e('Área:', 'thabatta-adv'); ?></label></th>
            <td>
                <select id="area" name="area">
                    <option value=""><?php esc_html_e('Selecione', 'thabatta-adv'); ?></option>
                    <option value="civil" <?php selected($area, 'civil'); ?>><?php esc_html_e('Civil', 'thabatta-adv'); ?></option>
                    <option value="previdenciario" <?php selected($area, 'previdenciario'); ?>><?php esc_html_e('Previdenciário', 'thabatta-adv'); ?></option>
                    <option value="trabalhista" <?php selected($area, 'trabalhista'); ?>><?php esc_html_e('Trabalhista', 'thabatta-adv'); ?></option>
                    <option value="consumidor" <?php selected($area, 'consumidor'); ?>><?php esc_html_e('Consumidor', 'thabatta-adv'); ?></option>
                    <option value="outro" <?php selected($area, 'outro'); ?>><?php esc_html_e('Outro', 'thabatta-adv'); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Salvar dados da metabox
 */
function thabatta_save_lead_details($post_id) {
    // Verificar se é um salvamento automático
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Verificar nonce
    if (!isset($_POST['thabatta_lead_details_nonce']) || !wp_verify_nonce($_POST['thabatta_lead_details_nonce'], 'thabatta_lead_details')) {
        return;
    }
    
    // Verificar permissões
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Salvar campos
    if (isset($_POST['phone'])) {
        update_post_meta($post_id, '_phone', sanitize_text_field($_POST['phone']));
    }
    
    if (isset($_POST['cpf_cnpj'])) {
        update_post_meta($post_id, '_cpf_cnpj', sanitize_text_field($_POST['cpf_cnpj']));
    }
    
    if (isset($_POST['area'])) {
        update_post_meta($post_id, '_area', sanitize_text_field($_POST['area']));
    }
}
add_action('save_post_lead', 'thabatta_save_lead_details');

/**
 * Handler AJAX para formulário de consulta
 */
function thabatta_handle_lead_form() {
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'thabatta_nonce')) {
        wp_send_json_error(array('message' => esc_html__('Verificação de segurança falhou. Por favor, tente novamente.', 'thabatta-adv')));
        return;
    }
    
    // Recuperar e sanitizar dados
    parse_str($_POST['formData'], $form_data);
    
    $name = isset($form_data['name']) ? sanitize_text_field($form_data['name']) : '';
    $phone = isset($form_data['phone']) ? sanitize_text_field($form_data['phone']) : '';
    $cpf_cnpj = isset($form_data['cpf_cnpj']) ? sanitize_text_field($form_data['cpf_cnpj']) : '';
    $case_details = isset($form_data['caseDetails']) ? sanitize_textarea_field($form_data['caseDetails']) : '';
    $law_area = isset($form_data['lawArea']) ? sanitize_text_field($form_data['lawArea']) : '';
    
    // Validar campos obrigatórios
    if (empty($name) || empty($phone) || empty($cpf_cnpj) || empty($case_details) || empty($law_area)) {
        wp_send_json_error(array('message' => esc_html__('Por favor, preencha todos os campos obrigatórios.', 'thabatta-adv')));
        return;
    }
    
    // Criar novo post de lead
    $lead_data = array(
        'post_title'   => $name,
        'post_content' => $case_details,
        'post_status'  => 'publish',
        'post_type'    => 'lead',
    );
    
    $lead_id = wp_insert_post($lead_data);
    
    if (is_wp_error($lead_id)) {
        wp_send_json_error(array('message' => esc_html__('Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente.', 'thabatta-adv')));
        return;
    }
    
    // Salvar campos meta
    update_post_meta($lead_id, '_phone', $phone);
    update_post_meta($lead_id, '_cpf_cnpj', $cpf_cnpj);
    update_post_meta($lead_id, '_area', $law_area);
    
    // Enviar e-mail de notificação
    thabatta_send_lead_notification($lead_id, $name, $phone, $cpf_cnpj, $case_details, $law_area);
    
    // Retornar sucesso
    wp_send_json_success(array('message' => esc_html__('Consulta enviada com sucesso!', 'thabatta-adv')));
}
add_action('wp_ajax_thabatta_lead', 'thabatta_handle_lead_form');
add_action('wp_ajax_nopriv_thabatta_lead', 'thabatta_handle_lead_form');

/**
 * Enviar e-mail de notificação para novos leads
 */
function thabatta_send_lead_notification($lead_id, $name, $phone, $cpf_cnpj, $case_details, $law_area) {
    // Obter destinatário do e-mail (Opções ACF)
    $admin_email = get_option('admin_email');
    
    if (function_exists('get_field')) {
        $notification_email = get_field('email_notificacao_leads', 'option');
        
        if (!empty($notification_email)) {
            $admin_email = $notification_email;
        }
    }
    
    // Mapear área para nome legível
    $area_names = array(
        'civil' => esc_html__('Civil', 'thabatta-adv'),
        'previdenciario' => esc_html__('Previdenciário', 'thabatta-adv'),
        'trabalhista' => esc_html__('Trabalhista', 'thabatta-adv'),
        'consumidor' => esc_html__('Consumidor', 'thabatta-adv'),
        'outro' => esc_html__('Outro', 'thabatta-adv'),
    );
    
    $area_name = isset($area_names[$law_area]) ? $area_names[$law_area] : $law_area;
    
    // Construir e-mail
    $subject = sprintf(esc_html__('Nova Consulta: %s', 'thabatta-adv'), $name);
    
    $message = sprintf(esc_html__('Nova consulta recebida pelo site.', 'thabatta-adv')) . "\n\n";
    $message .= sprintf(esc_html__('Nome: %s', 'thabatta-adv'), $name) . "\n";
    $message .= sprintf(esc_html__('Telefone: %s', 'thabatta-adv'), $phone) . "\n";
    $message .= sprintf(esc_html__('CPF/CNPJ: %s', 'thabatta-adv'), $cpf_cnpj) . "\n";
    $message .= sprintf(esc_html__('Área: %s', 'thabatta-adv'), $area_name) . "\n\n";
    $message .= sprintf(esc_html__('Detalhes do Caso:', 'thabatta-adv')) . "\n";
    $message .= $case_details . "\n\n";
    $message .= sprintf(esc_html__('Acesse o painel para visualizar todos os detalhes:', 'thabatta-adv')) . "\n";
    $message .= admin_url('post.php?post=' . $lead_id . '&action=edit') . "\n";
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    // Enviar e-mail
    wp_mail($admin_email, $subject, $message, $headers);
}