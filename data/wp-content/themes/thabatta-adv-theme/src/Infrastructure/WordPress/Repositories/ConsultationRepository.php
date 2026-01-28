<?php

namespace ThabattaAdv\Infrastructure\WordPress\Repositories;

use ThabattaAdv\Application\Consultation\ConsultationRequest;

class ConsultationRepository
{
    public function create(ConsultationRequest $request): int
    {
        $postData = [
            'post_title'   => sprintf(
                __('Consulta: %s (%s)', 'thabatta-adv'),
                $request->name(),
                date_i18n('d/m/Y H:i')
            ),
            'post_content' => $request->caseDetails(),
            'post_status'  => 'private',
            'post_type'    => 'consultation',
        ];

        $postId = wp_insert_post($postData, true);

        if (is_wp_error($postId)) {
            throw new \RuntimeException(
                __('Erro ao registrar sua consulta. Por favor, tente novamente.', 'thabatta-adv')
            );
        }

        update_post_meta($postId, '_consultation_name', $request->name());
        update_post_meta($postId, '_consultation_email', $request->email());
        update_post_meta($postId, '_consultation_phone', $request->phone());
        update_post_meta($postId, '_consultation_cpf_cnpj', $request->cpfCnpj());
        update_post_meta($postId, '_consultation_area', $request->lawArea());
        update_post_meta($postId, '_consultation_urgency', $request->urgency());
        update_post_meta($postId, '_consultation_contact_preference', $request->contactPreference());
        update_post_meta($postId, '_consultation_date', current_time('mysql'));
        update_post_meta($postId, '_consultation_status', 'new');

        return (int) $postId;
    }
}
