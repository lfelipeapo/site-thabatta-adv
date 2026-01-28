<?php

namespace ThabattaAdv\Application\Consultation;

use ThabattaAdv\Infrastructure\WordPress\Mailers\ConsultationMailer;
use ThabattaAdv\Infrastructure\WordPress\Repositories\ConsultationRepository;

class SubmitConsultation
{
    private ConsultationRepository $repository;
    private ConsultationMailer $mailer;

    public function __construct(
        ConsultationRepository $repository,
        ConsultationMailer $mailer
    ) {
        $this->repository = $repository;
        $this->mailer = $mailer;
    }

    public function handle(ConsultationRequest $request): int
    {
        $postId = $this->repository->create($request);

        $this->mailer->notifyAdmin($request, $postId);
        $this->mailer->notifyClient($request);

        return $postId;
    }
}
