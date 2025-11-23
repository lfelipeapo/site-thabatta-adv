<?php
/**
 * Template para o formulário de contato simples
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}
?>

<div class="contact-form-wrapper">
    <form id="contactForm" class="contact-form" method="post">
        <?php wp_nonce_field('thabatta_contact_nonce', 'contact_nonce'); ?>
        
        <div class="form-row">
            <div class="form-group">
                <label for="contact_name"><?php esc_html_e('Nome', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="text" id="contact_name" name="contact_name" required>
            </div>
            
            <div class="form-group">
                <label for="contact_email"><?php esc_html_e('Email', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="email" id="contact_email" name="contact_email" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="contact_phone"><?php esc_html_e('Telefone', 'thabatta-adv'); ?></label>
                <input type="tel" id="contact_phone" name="contact_phone" class="phone-mask">
            </div>
            
            <div class="form-group">
                <label for="contact_subject"><?php esc_html_e('Assunto', 'thabatta-adv'); ?> <span class="required">*</span></label>
                <input type="text" id="contact_subject" name="contact_subject" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="contact_message"><?php esc_html_e('Mensagem', 'thabatta-adv'); ?> <span class="required">*</span></label>
            <textarea id="contact_message" name="contact_message" rows="5" required></textarea>
        </div>
        
        <div class="form-check">
            <input type="checkbox" id="contact_privacy" name="contact_privacy" required>
            <label for="contact_privacy">
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
        
        <div class="form-submit">
            <button type="submit" class="btn btn-primary"><?php esc_html_e('Enviar Mensagem', 'thabatta-adv'); ?></button>
        </div>
        
        <div id="contactResponse" class="form-response"></div>
    </form>
</div> 