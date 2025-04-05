<?php
/**
 * Template para o formulário de consulta multi-etapas
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}
?>

<div id="consultationForm" class="consultation-form">
    <div class="form-overlay"></div>
    <div class="form-container">
        <button type="button" class="close-form" aria-label="<?php esc_attr_e('Fechar', 'thabatta-adv'); ?>">
            <span aria-hidden="true">&times;</span>
        </button>
        
        <h3><?php esc_html_e('Solicite uma consulta', 'thabatta-adv'); ?></h3>
        
        <div class="step-indicators">
            <div class="step-indicator active" data-step="1">
                <span class="step-number">1</span>
                <span class="step-text"><?php esc_html_e('Dados Pessoais', 'thabatta-adv'); ?></span>
            </div>
            <div class="step-indicator" data-step="2">
                <span class="step-number">2</span>
                <span class="step-text"><?php esc_html_e('Documentação', 'thabatta-adv'); ?></span>
            </div>
            <div class="step-indicator" data-step="3">
                <span class="step-number">3</span>
                <span class="step-text"><?php esc_html_e('Seu Caso', 'thabatta-adv'); ?></span>
            </div>
        </div>
        
        <form id="multiStepForm" class="multi-step-form">
            <?php wp_nonce_field('thabatta_nonce', 'consultation_nonce'); ?>
            
            <!-- Etapa 1: Dados Pessoais -->
            <div class="step active" data-step="1">
                <div class="form-group">
                    <label for="name"><?php esc_html_e('Nome Completo', 'thabatta-adv'); ?> <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><?php esc_html_e('E-mail', 'thabatta-adv'); ?> <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone"><?php esc_html_e('Telefone', 'thabatta-adv'); ?> <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" class="phone-mask" required>
                </div>
            </div>
            
            <!-- Etapa 2: Documentação -->
            <div class="step" data-step="2">
                <div class="form-group">
                    <label for="cpf_cnpj"><?php esc_html_e('CPF ou CNPJ', 'thabatta-adv'); ?> <span class="required">*</span></label>
                    <input type="text" id="cpf_cnpj" name="cpf_cnpj" class="cpfcnpj-mask" required>
                </div>
                
                <div class="form-group">
                    <label for="lawArea"><?php esc_html_e('Área de Atuação', 'thabatta-adv'); ?> <span class="required">*</span></label>
                    <select id="lawArea" name="lawArea" required>
                        <option value=""><?php esc_html_e('Selecione', 'thabatta-adv'); ?></option>
                        <option value="civil"><?php esc_html_e('Civil', 'thabatta-adv'); ?></option>
                        <option value="previdenciario"><?php esc_html_e('Previdenciário', 'thabatta-adv'); ?></option>
                        <option value="trabalhista"><?php esc_html_e('Trabalhista', 'thabatta-adv'); ?></option>
                        <option value="consumidor"><?php esc_html_e('Consumidor', 'thabatta-adv'); ?></option>
                        <option value="outro"><?php esc_html_e('Outro', 'thabatta-adv'); ?></option>
                    </select>
                </div>
            </div>
            
            <!-- Etapa 3: Seu Caso -->
            <div class="step" data-step="3">
                <div class="form-group">
                    <label for="caseDetails"><?php esc_html_e('Detalhes do Caso', 'thabatta-adv'); ?> <span class="required">*</span></label>
                    <textarea id="caseDetails" name="caseDetails" rows="5" required></textarea>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" id="privacy" name="privacy" required>
                    <label for="privacy">
                        <?php 
                        printf(
                            esc_html__('Concordo com a %sPolítica de Privacidade%s', 'thabatta-adv'),
                            '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">',
                            '</a>'
                        );
                        ?>
                        <span class="required">*</span>
                    </label>
                </div>
            </div>
            
            <!-- Botões de navegação -->
            <div class="form-nav">
                <button type="button" id="prevBtn" class="btn btn-outline hidden"><?php esc_html_e('Anterior', 'thabatta-adv'); ?></button>
                <button type="button" id="nextBtn" class="btn btn-primary"><?php esc_html_e('Próximo', 'thabatta-adv'); ?></button>
                <button type="submit" id="submitBtn" class="btn btn-primary hidden"><?php esc_html_e('Enviar', 'thabatta-adv'); ?></button>
            </div>
        </form>
        
        <!-- Mensagem de sucesso (inicialmente oculta) -->
        <div id="formSuccess" class="form-success hidden">
            <div class="success-icon">
                <i class="fa fa-check-circle"></i>
            </div>
            <h4><?php esc_html_e('Consulta Enviada com Sucesso!', 'thabatta-adv'); ?></h4>
            <p><?php esc_html_e('Obrigado pelo seu contato. Entraremos em contato em breve para agendar sua consulta.', 'thabatta-adv'); ?></p>
            <button type="button" class="btn btn-primary close-success"><?php esc_html_e('Fechar', 'thabatta-adv'); ?></button>
        </div>
    </div>
</div> 