<?php

declare(strict_types=1);

namespace Application\Consultation\Actions;

use Domain\Consultation\Entities\ConsultationRequest;
use Domain\Consultation\Repositories\ConsultationRequestRepository;
use Domain\Consultation\ValueObjects\ConsultationReference;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Shared\Exceptions\DomainException;

/**
 * Moves a consultation request through its qualification pipeline on
 * staff's behalf. The legality of the move is entirely the aggregate's
 * concern — see Domain\Consultation\ValueObjects\ConsultationStatus::
 * allowedTransitions() — this action only loads, delegates, and persists.
 */
final readonly class TransitionConsultationStatus
{
    public function __construct(private ConsultationRequestRepository $requests) {}

    /** @throws DomainException when the request does not exist or the transition is illegal. */
    public function __invoke(string $reference, ConsultationStatus $target): ConsultationRequest
    {
        $request = $this->requests->findByReference(ConsultationReference::fromString($reference));

        if ($request === null) {
            throw new DomainException(sprintf('No consultation request exists with reference "%s".', $reference));
        }

        $request->transitionTo($target);

        $this->requests->save($request);

        return $request;
    }
}
