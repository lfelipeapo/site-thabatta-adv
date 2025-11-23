<?php
/**
 * Formulário de contato multi-etapas para página de contato
 *
 * @package Thabatta_Advocacia
 */
if (!defined('ABSPATH')) exit;
?>
<div class="contact-multistep-form-wrapper">
    <h3 class="form-title"><?php esc_html_e('Entre em contato', 'thabatta-adv'); ?></h3>
    <div class="step-indicators">
        <div class="step-indicator active" data-step="1">1</div>
        <div class="step-indicator" data-step="2">2</div>
        <div class="step-indicator" data-step="3">3</div>
    </div>
    <form id="contactMultiStepForm" class="multi-step-form" method="post" novalidate>
        <?php wp_nonce_field('thabatta_consultation_nonce', 'security'); ?>
        <!-- Etapa 1: Dados Pessoais -->
        <div class="step active" data-step="1">
            <h4 class="step-title"><?php esc_html_e('Dados Pessoais', 'thabatta-adv'); ?></h4>
            <div class="form-group">
                <label for="name"><?php esc_html_e('Nome Completo', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required data-error="<?php esc_attr_e('Por favor, informe seu nome completo', 'thabatta-adv'); ?>">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label for="email"><?php esc_html_e('E-mail', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-control" required data-error="<?php esc_attr_e('Por favor, informe um e-mail válido', 'thabatta-adv'); ?>">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label for="phone"><?php esc_html_e('Telefone', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" class="form-control phone-mask" required data-error="<?php esc_attr_e('Por favor, informe um telefone válido', 'thabatta-adv'); ?>">
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <!-- Etapa 2: Área de Atuação -->
        <div class="step" data-step="2">
            <h4 class="step-title"><?php esc_html_e('Área de Atuação', 'thabatta-adv'); ?></h4>
            <div class="form-group">
                <label for="cpfcnpj"><?php esc_html_e('CPF ou CNPJ', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="text" id="cpfcnpj" name="cpfcnpj" class="form-control cpfcnpj-mask" required data-error="<?php esc_attr_e('Por favor, informe um CPF ou CNPJ válido', 'thabatta-adv'); ?>">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <label for="area"><?php esc_html_e('Área de Atuação', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <select id="area" name="area" class="form-control" required data-error="<?php esc_attr_e('Por favor, selecione uma área de atuação', 'thabatta-adv'); ?>">
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
                <label for="urgency"><?php esc_html_e('Urgência', 'thabatta-adv'); ?></label>
                <select id="urgency" name="urgency" class="form-control">
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
                <label for="message"><?php esc_html_e('Detalhes do Caso', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <textarea id="message" name="message" class="form-control" rows="5" required data-error="<?php esc_attr_e('Por favor, descreva brevemente seu caso', 'thabatta-adv'); ?>"></textarea>
                <div class="invalid-feedback"></div>
                <small class="form-text text-muted"><?php esc_html_e('Descreva brevemente seu caso para que possamos nos preparar para a consulta.', 'thabatta-adv'); ?></small>
            </div>
            <div class="form-group">
                <label for="contact_preference"><?php esc_html_e('Forma de Contato Preferida', 'thabatta-adv'); ?></label>
                <select id="contact_preference" name="contact_preference" class="form-control">
                    <option value="phone"><?php esc_html_e('Telefone', 'thabatta-adv'); ?></option>
                    <option value="email"><?php esc_html_e('E-mail', 'thabatta-adv'); ?></option>
                    <option value="whatsapp"><?php esc_html_e('WhatsApp', 'thabatta-adv'); ?></option>
                </select>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="confirmation" name="confirmation" required data-error="<?php esc_attr_e('Você precisa concordar com a política de privacidade', 'thabatta-adv'); ?>">
                <label class="form-check-label" for="confirmation">
                    <?php printf(esc_html__('Concordo com a %sPolítica de Privacidade%s', 'thabatta-adv'), '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">', '</a>'); ?>
                    <span class="required">*</span>
                </label>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <!-- Botões de navegação -->
        <div class="form-nav">
            <button type="button" id="contactPrevBtn" class="btn btn-outline-secondary hidden"><?php esc_html_e('Anterior', 'thabatta-adv'); ?></button>
            <button type="button" id="contactNextBtn" class="btn btn-primary"><?php esc_html_e('Próximo', 'thabatta-adv'); ?></button>
            <button type="submit" id="contactSubmitBtn" class="btn btn-primary hidden"><?php esc_html_e('Enviar', 'thabatta-adv'); ?></button>
        </div>
    </form>
    <div id="contactFormSuccess" class="form-success hidden">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h4><?php esc_html_e('Consulta enviada com sucesso!', 'thabatta-adv'); ?></h4>
        <p><?php esc_html_e('Obrigado pelo seu contato. Em breve retornaremos.', 'thabatta-adv'); ?></p>
    </div>
</div> 