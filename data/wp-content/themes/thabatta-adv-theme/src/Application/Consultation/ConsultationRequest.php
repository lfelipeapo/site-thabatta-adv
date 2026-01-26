<?php

namespace ThabattaAdv\Application\Consultation;

class ConsultationRequest
{
    private string $name;
    private string $email;
    private string $phone;
    private string $cpfCnpj;
    private string $lawArea;
    private string $urgency;
    private string $caseDetails;
    private string $contactPreference;
    private bool $confirmation;

    public function __construct(
        string $name,
        string $email,
        string $phone,
        string $cpfCnpj,
        string $lawArea,
        string $urgency,
        string $caseDetails,
        string $contactPreference,
        bool $confirmation
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->cpfCnpj = $cpfCnpj;
        $this->lawArea = $lawArea;
        $this->urgency = $urgency;
        $this->caseDetails = $caseDetails;
        $this->contactPreference = $contactPreference;
        $this->confirmation = $confirmation;
    }

    public static function fromPost(array $payload): self
    {
        $name = isset($payload['name']) ? sanitize_text_field($payload['name']) : '';
        $email = isset($payload['email']) ? sanitize_email($payload['email']) : '';
        $phone = isset($payload['phone']) ? sanitize_text_field($payload['phone']) : '';
        $cpfCnpj = isset($payload['cpfcnpj']) ? sanitize_text_field($payload['cpfcnpj']) : '';
        $lawArea = isset($payload['area']) ? sanitize_text_field($payload['area']) : '';
        $urgency = isset($payload['urgency']) ? sanitize_text_field($payload['urgency']) : 'media';
        $caseDetails = isset($payload['message']) ? sanitize_textarea_field($payload['message']) : '';
        $contactPreference = isset($payload['contact_preference'])
            ? sanitize_text_field($payload['contact_preference'])
            : 'phone';
        $confirmation = isset($payload['confirmation']) && $payload['confirmation'] === 'on';

        if ($name === '' || $email === '' || $phone === '' || $lawArea === '' || !$confirmation) {
            throw new \InvalidArgumentException(
                __('Por favor, preencha todos os campos obrigatórios.', 'thabatta-adv')
            );
        }

        if (!is_email($email)) {
            throw new \InvalidArgumentException(
                __('Por favor, forneça um endereço de e-mail válido.', 'thabatta-adv')
            );
        }

        $urgency = self::normalizeUrgency($urgency);
        $contactPreference = self::normalizeContactPreference($contactPreference);

        return new self(
            $name,
            $email,
            $phone,
            $cpfCnpj,
            $lawArea,
            $urgency,
            $caseDetails,
            $contactPreference,
            $confirmation
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function phone(): string
    {
        return $this->phone;
    }

    public function cpfCnpj(): string
    {
        return $this->cpfCnpj;
    }

    public function lawArea(): string
    {
        return $this->lawArea;
    }

    public function urgency(): string
    {
        return $this->urgency;
    }

    public function caseDetails(): string
    {
        return $this->caseDetails;
    }

    public function contactPreference(): string
    {
        return $this->contactPreference;
    }

    public function confirmation(): bool
    {
        return $this->confirmation;
    }

    private static function normalizeUrgency(string $urgency): string
    {
        $allowed = ['baixa', 'media', 'alta'];

        return in_array($urgency, $allowed, true) ? $urgency : 'media';
    }

    private static function normalizeContactPreference(string $contactPreference): string
    {
        $allowed = ['phone', 'email', 'whatsapp'];

        return in_array($contactPreference, $allowed, true) ? $contactPreference : 'phone';
    }
}
