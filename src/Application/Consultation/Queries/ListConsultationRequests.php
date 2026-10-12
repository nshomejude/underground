<?php

declare(strict_types=1);

namespace Application\Consultation\Queries;

use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\Repositories\ConsultationRequestRepository;

/** Backs the staff review queue: every consultation request, newest first. */
final readonly class ListConsultationRequests
{
    public function __construct(private ConsultationRequestRepository $requests) {}

    /** @return list<ConsultationRequest> */
    public function __invoke(): array
    {
        return $this->requests->all();
    }
}
