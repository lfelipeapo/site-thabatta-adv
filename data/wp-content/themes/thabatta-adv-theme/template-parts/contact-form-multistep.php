<?php
/**
 * Formulário de contato multi-etapas para página de contato
 *
 * @package Thabatta_Advocacia
 */
if (!defined('ABSPATH')) {
    exit;
}

$args = [
    'wrapper_class' => 'contact-multistep-form-wrapper',
    'title' => __('Entre em contato', 'thabatta-adv'),
    'title_tag' => 'h3',
    'id_prefix' => 'contact',
    'form_id' => 'contactMultiStepForm',
    'nonce_action' => 'thabatta_consultation_nonce',
    'nonce_name' => 'security',
    'prev_button_id' => 'contactPrevBtn',
    'next_button_id' => 'contactNextBtn',
    'submit_button_id' => 'contactSubmitBtn',
    'prev_button_label' => __('Anterior', 'thabatta-adv'),
    'next_button_label' => __('Próximo', 'thabatta-adv'),
    'submit_button_label' => __('Enviar', 'thabatta-adv'),
    'success_id' => 'contactFormSuccess',
    'success_title' => __('Consulta enviada com sucesso!', 'thabatta-adv'),
    'success_message' => __('Obrigado pelo seu contato. Em breve retornaremos.', 'thabatta-adv'),
];

get_template_part('template-parts/components/multistep-form', null, $args);
