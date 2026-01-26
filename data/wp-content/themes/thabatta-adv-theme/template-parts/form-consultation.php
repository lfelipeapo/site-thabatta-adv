<?php
/**
 * Template para o formulário de consulta multi-etapas
 *
 * @package Thabatta_Advocacia
 */

if (!defined('ABSPATH')) {
    exit; // Saída direta se acessado diretamente
}

$args = [
    'wrapper_id' => 'consultationForm',
    'wrapper_class' => 'consultation-form',
    'container_class' => 'form-container',
    'show_overlay' => true,
    'show_close_button' => true,
    'close_button_aria_label' => __('Fechar', 'thabatta-adv'),
    'title' => __('Solicite uma consulta', 'thabatta-adv'),
    'title_tag' => 'h3',
    'id_prefix' => 'consultation',
    'form_id' => 'multiStepForm',
    'nonce_action' => 'thabatta_consultation_nonce',
    'nonce_name' => 'security',
    'email_pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$',
    'prev_button_id' => 'prevBtn',
    'next_button_id' => 'nextBtn',
    'submit_button_id' => 'submitBtn',
    'prev_button_label' => __('Anterior', 'thabatta-adv'),
    'next_button_label' => __('Próximo', 'thabatta-adv'),
    'submit_button_label' => __('Enviar', 'thabatta-adv'),
    'success_id' => 'formSuccess',
    'success_title' => __('Consulta Enviada com Sucesso!', 'thabatta-adv'),
    'success_message' => __('Obrigado pelo seu contato. Entraremos em contato em breve para agendar sua consulta.', 'thabatta-adv'),
    'show_success_close' => true,
    'success_close_label' => __('Fechar', 'thabatta-adv'),
];

get_template_part('template-parts/components/multistep-form', null, $args);
