<?php

declare(strict_types=1);

namespace Domain\Consultation\Repositories;

use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\ValueObjects\ConsultationReference;

interface ConsultationRequestRepository
{
    public function save(ConsultationRequest $request): void;

    public function findByReference(ConsultationReference $reference): ?ConsultationRequest;

    /** Every consultation request, most recently submitted first — backs the staff review queue. */
    public function all(): array;
}
