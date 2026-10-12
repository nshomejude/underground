<?php

declare(strict_types=1);

namespace Application\Consultation\Queries;

use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\Repositories\ConsultationRequestRepository;
use Domain\Consultation\ValueObjects\ConsultationReference;

final readonly class TrackConsultationRequest
{
    public function __construct(private ConsultationRequestRepository $requests) {}

    public function __invoke(string $reference): ?ConsultationRequest
    {
        return $this->requests->findByReference(
            ConsultationReference::fromString($reference),
        );
    }
}
