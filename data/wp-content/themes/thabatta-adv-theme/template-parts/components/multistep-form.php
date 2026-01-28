<?php
/**
 * Template part para formulário multi-etapas reutilizável.
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit;
}

$defaults = [
    'wrapper_id' => '',
    'wrapper_class' => '',
    'container_class' => '',
    'show_overlay' => false,
    'show_close_button' => false,
    'close_button_aria_label' => __('Fechar', 'thabatta-adv'),
    'title' => '',
    'title_tag' => 'h3',
    'id_prefix' => 'multistep',
    'form_id' => '',
    'nonce_action' => 'thabatta_consultation_nonce',
    'nonce_name' => 'security',
    'email_pattern' => '',
    'prev_button_id' => '',
    'next_button_id' => '',
    'submit_button_id' => '',
    'prev_button_label' => __('Anterior', 'thabatta-adv'),
    'next_button_label' => __('Próximo', 'thabatta-adv'),
    'submit_button_label' => __('Enviar', 'thabatta-adv'),
    'success_id' => '',
    'success_title' => '',
    'success_message' => '',
    'show_success_close' => false,
    'success_close_label' => __('Fechar', 'thabatta-adv'),
];

$args = wp_parse_args($args ?? [], $defaults);
$title_tag = in_array($args['title_tag'], ['h2', 'h3', 'h4'], true) ? $args['title_tag'] : 'h3';
$id_prefix = sanitize_key($args['id_prefix']);

$name_id = $id_prefix . '-name';
$email_id = $id_prefix . '-email';
$phone_id = $id_prefix . '-phone';
$cpfcnpj_id = $id_prefix . '-cpfcnpj';
$area_id = $id_prefix . '-area';
$urgency_id = $id_prefix . '-urgency';
$message_id = $id_prefix . '-message';
$contact_preference_id = $id_prefix . '-contact-preference';
$confirmation_id = $id_prefix . '-confirmation';
?>

<div<?php echo $args['wrapper_id'] ? ' id="' . esc_attr($args['wrapper_id']) . '"' : ''; ?> class="<?php echo esc_attr($args['wrapper_class']); ?>">
    <?php if ($args['show_overlay']) : ?>
        <div class="form-overlay"></div>
    <?php endif; ?>

    <?php if ($args['container_class']) : ?>
        <div class="<?php echo esc_attr($args['container_class']); ?>">
    <?php endif; ?>

    <?php if ($args['show_close_button']) : ?>
        <button type="button" class="close-form" aria-label="<?php echo esc_attr($args['close_button_aria_label']); ?>">
            <i class="fas fa-times"></i>
        </button>
    <?php endif; ?>

    <<?php echo esc_html($title_tag); ?> class="form-title"><?php echo esc_html($args['title']); ?></<?php echo esc_html($title_tag); ?>>

    <div class="step-indicators">
        <div class="step-indicator active" data-step="1">1</div>
        <div class="step-indicator" data-step="2">2</div>
        <div class="step-indicator" data-step="3">3</div>
    </div>

    <form id="<?php echo esc_attr($args['form_id']); ?>" class="multi-step-form" method="post" novalidate>
        <?php wp_nonce_field($args['nonce_action'], $args['nonce_name']); ?>

        <!-- Etapa 1: Dados Pessoais -->
        <div class="step active" data-step="1">
            <h4 class="step-title"><?php esc_html_e('Dados Pessoais', 'thabatta-adv'); ?></h4>

            <div class="form-group">
                <label for="<?php echo esc_attr($name_id); ?>"><?php esc_html_e('Nome Completo', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="text" id="<?php echo esc_attr($name_id); ?>" name="name" class="form-control" required
                    data-error="<?php esc_attr_e('Por favor, informe seu nome completo', 'thabatta-adv'); ?>">
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label for="<?php echo esc_attr($email_id); ?>"><?php esc_html_e('E-mail', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="email" id="<?php echo esc_attr($email_id); ?>" name="email" class="form-control" required
                    <?php if (!empty($args['email_pattern'])) : ?>pattern="<?php echo esc_attr($args['email_pattern']); ?>"<?php endif; ?>
                    data-error="<?php esc_attr_e('Por favor, informe um e-mail válido', 'thabatta-adv'); ?>">
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label for="<?php echo esc_attr($phone_id); ?>"><?php esc_html_e('Telefone', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="tel" id="<?php echo esc_attr($phone_id); ?>" name="phone" class="form-control phone-mask" required
                    data-error="<?php esc_attr_e('Por favor, informe um telefone válido', 'thabatta-adv'); ?>">
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Etapa 2: Área de Atuação -->
        <div class="step" data-step="2">
            <h4 class="step-title"><?php esc_html_e('Área de Atuação', 'thabatta-adv'); ?></h4>

            <div class="form-group">
                <label for="<?php echo esc_attr($cpfcnpj_id); ?>"><?php esc_html_e('CPF ou CNPJ', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="text" id="<?php echo esc_attr($cpfcnpj_id); ?>" name="cpfcnpj" class="form-control cpfcnpj-mask" required
                    data-error="<?php esc_attr_e('Por favor, informe um CPF ou CNPJ válido', 'thabatta-adv'); ?>">
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label for="<?php echo esc_attr($area_id); ?>"><?php esc_html_e('Área de Atuação', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <select id="<?php echo esc_attr($area_id); ?>" name="area" class="form-control" required
                    data-error="<?php esc_attr_e('Por favor, selecione uma área de atuação', 'thabatta-adv'); ?>">
                    <option value=""><?php esc_html_e('Selecione', 'thabatta-adv'); ?></option>
                    <?php
                    $areas = get_posts(array(
                        'post_type' => 'area_atuacao',
                        'numberposts' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC'
                    ));

                    if ($areas) {
                        foreach ($areas as $area) {
                            echo '<option value="' . esc_attr($area->post_title) . '">' . esc_html($area->post_title) . '</option>';
                        }
                    } else {
                        $default_areas = array(
                            'civil' => 'Direito Civil',
                            'empresarial' => 'Direito Empresarial',
                            'trabalhista' => 'Direito Trabalhista',
                            'consumidor' => 'Direito do Consumidor',
                            'previdenciario' => 'Direito Previdenciário',
                            'outro' => 'Outro'
                        );

                        foreach ($default_areas as $value => $label) {
                            echo '<option value="' . esc_html($label) . '">' . esc_html($label) . '</option>';
                        }
                    }
                    ?>
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label for="<?php echo esc_attr($urgency_id); ?>"><?php esc_html_e('Urgência', 'thabatta-adv'); ?></label>
                <select id="<?php echo esc_attr($urgency_id); ?>" name="urgency" class="form-control">
                    <option value="baixa"><?php esc_html_e('Baixa - Consulta informativa', 'thabatta-adv'); ?></option>
                    <option value="media" selected><?php esc_html_e('Média - Preciso resolver nas próximas semanas', 'thabatta-adv'); ?></option>
                    <option value="alta"><?php esc_html_e('Alta - Tenho prazos críticos', 'thabatta-adv'); ?></option>
                </select>
            </div>
        </div>

        <!-- Etapa 3: Seu Caso -->
        <div class="step" data-step="3">
            <h4 class="step-title"><?php esc_html_e('Seu Caso', 'thabatta-adv'); ?></h4>

            <div class="form-group">
                <label for="<?php echo esc_attr($message_id); ?>"><?php esc_html_e('Detalhes do Caso', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <textarea id="<?php echo esc_attr($message_id); ?>" name="message" class="form-control" rows="5" required
                    data-error="<?php esc_attr_e('Por favor, descreva brevemente seu caso', 'thabatta-adv'); ?>"></textarea>
                <div class="invalid-feedback"></div>
                <small class="form-text text-muted"><?php esc_html_e('Descreva brevemente seu caso para que possamos nos preparar para a consulta.', 'thabatta-adv'); ?></small>
            </div>

            <div class="form-group">
                <label for="<?php echo esc_attr($contact_preference_id); ?>"><?php esc_html_e('Forma de Contato Preferida', 'thabatta-adv'); ?></label>
                <select id="<?php echo esc_attr($contact_preference_id); ?>" name="contact_preference" class="form-control">
                    <option value="phone"><?php esc_html_e('Telefone', 'thabatta-adv'); ?></option>
                    <option value="email"><?php esc_html_e('E-mail', 'thabatta-adv'); ?></option>
                    <option value="whatsapp"><?php esc_html_e('WhatsApp', 'thabatta-adv'); ?></option>
                </select>
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="<?php echo esc_attr($confirmation_id); ?>" name="confirmation" required
                    data-error="<?php esc_attr_e('Você precisa concordar com a política de privacidade', 'thabatta-adv'); ?>">
                <label class="form-check-label" for="<?php echo esc_attr($confirmation_id); ?>">
                    <?php
                    printf(
                        esc_html__('Concordo com a %sPolítica de Privacidade%s', 'thabatta-adv'),
                        '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">',
                        '</a>'
                    );
                    ?>
                    <span class="required">*</span>
                </label>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <!-- Botões de navegação -->
        <div class="form-nav">
            <button type="button" id="<?php echo esc_attr($args['prev_button_id']); ?>" class="btn btn-outline-secondary hidden"><?php echo esc_html($args['prev_button_label']); ?></button>
            <button type="button" id="<?php echo esc_attr($args['next_button_id']); ?>" class="btn btn-primary"><?php echo esc_html($args['next_button_label']); ?></button>
            <button type="submit" id="<?php echo esc_attr($args['submit_button_id']); ?>" class="btn btn-primary hidden"><?php echo esc_html($args['submit_button_label']); ?></button>
        </div>
    </form>

    <div id="<?php echo esc_attr($args['success_id']); ?>" class="form-success hidden">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h4><?php echo esc_html($args['success_title']); ?></h4>
        <p><?php echo esc_html($args['success_message']); ?></p>
        <?php if ($args['show_success_close']) : ?>
            <button type="button" class="btn btn-primary close-success"><?php echo esc_html($args['success_close_label']); ?></button>
        <?php endif; ?>
    </div>

    <?php if ($args['container_class']) : ?>
        </div>
    <?php endif; ?>
</div>
