<?php

namespace ThabattaAdv\Infrastructure\WordPress\Mailers;

use ThabattaAdv\Application\Consultation\ConsultationRequest;

class ConsultationMailer
{
    public function notifyAdmin(ConsultationRequest $request, int $postId): void
    {
        $adminEmail = get_option('admin_email');
        $siteName = get_bloginfo('name');
        $subject = sprintf(__('[%s] Nova solicitação de consulta', 'thabatta-adv'), $siteName);

        $message = sprintf(
            __('Nova solicitação de consulta recebida de %s', 'thabatta-adv'),
            $request->name()
        ) . "\n\n";
        $message .= __('Detalhes da solicitação:', 'thabatta-adv') . "\n";
        $message .= __('Nome: ', 'thabatta-adv') . $request->name() . "\n";
        $message .= __('E-mail: ', 'thabatta-adv') . $request->email() . "\n";
        $message .= __('Telefone: ', 'thabatta-adv') . $request->phone() . "\n";
        $message .= __('CPF/CNPJ: ', 'thabatta-adv') . $request->cpfCnpj() . "\n";
        $message .= __('Área de Atuação: ', 'thabatta-adv') . $request->lawArea() . "\n";
        $message .= __('Urgência: ', 'thabatta-adv') . $this->urgencyLabel($request->urgency()) . "\n";
        $message .= __('Forma de Contato Preferida: ', 'thabatta-adv')
            . $this->contactPreferenceLabel($request->contactPreference()) . "\n\n";

        if ($request->caseDetails() !== '') {
            $message .= __('Detalhes do Caso:', 'thabatta-adv') . "\n"
                . $request->caseDetails() . "\n\n";
        }

        $message .= __('Para gerenciar esta consulta, acesse o painel administrativo:', 'thabatta-adv') . "\n";
        $message .= admin_url('post.php?post=' . $postId . '&action=edit');

        wp_mail($adminEmail, $subject, $message, $this->headers());
    }

    public function notifyClient(ConsultationRequest $request): void
    {
        $siteName = get_bloginfo('name');
        $subject = sprintf(__('Sua solicitação de consulta foi recebida - %s', 'thabatta-adv'), $siteName);

        $message = sprintf(__('Olá %s,', 'thabatta-adv'), $request->name()) . "\n\n";
        $message .= __('Recebemos sua solicitação de consulta e agradecemos pelo seu interesse.', 'thabatta-adv')
            . "\n\n";
        $message .= __('Entraremos em contato em breve para agendar sua consulta.', 'thabatta-adv') . "\n\n";
        $message .= __('Resumo da sua solicitação:', 'thabatta-adv') . "\n";
        $message .= __('Área de Atuação: ', 'thabatta-adv') . $request->lawArea() . "\n";
        $message .= __('Urgência: ', 'thabatta-adv') . $this->urgencyLabel($request->urgency()) . "\n";
        $message .= __('Forma de Contato Preferida: ', 'thabatta-adv')
            . $this->contactPreferenceLabel($request->contactPreference()) . "\n\n";
        $message .= __('Atenciosamente,', 'thabatta-adv') . "\n";
        $message .= $siteName;

        wp_mail($request->email(), $subject, $message, $this->headers());
    }

    private function urgencyLabel(string $urgency): string
    {
        $labels = [
            'baixa' => __('Baixa - Consulta informativa', 'thabatta-adv'),
            'media' => __('Média - Preciso resolver nas próximas semanas', 'thabatta-adv'),
            'alta' => __('Alta - Tenho prazos críticos', 'thabatta-adv'),
        ];

        return $labels[$urgency] ?? $urgency;
    }

    private function contactPreferenceLabel(string $contactPreference): string
    {
        $labels = [
            'phone' => __('Telefone', 'thabatta-adv'),
            'email' => __('E-mail', 'thabatta-adv'),
            'whatsapp' => __('WhatsApp', 'thabatta-adv'),
        ];

        return $labels[$contactPreference] ?? $contactPreference;
    }

    private function headers(): array
    {
        return ['Content-Type: text/plain; charset=UTF-8'];
    }
}
