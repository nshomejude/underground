<?php

declare(strict_types=1);

namespace Domain\Consultation\Events;

use Domain\Consultation\Entities\ConsultationRequest;

/**
 * Raised once a consultation request is durably stored. Listeners route it
 * — the project and need descriptions never leave the aggregate.
 */
final readonly class ConsultationRequestSubmitted
{
    public function __construct(public ConsultationRequest $request) {}
}
