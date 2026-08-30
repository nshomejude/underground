<?php

declare(strict_types=1);

namespace Application\Engagement\Actions;

use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\Repositories\InquiryRepository;
use Domain\Engagement\ValueObjects\InquiryReference;
use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Shared\Exceptions\DomainException;

/**
 * Moves a confidential inquiry through its status machine on staff's behalf.
 * The legality of the move (e.g. Received -> UnderReview but never straight
 * to Engaged) is entirely the aggregate's concern — see
 * Domain\Engagement\ValueObjects\InquiryStatus::allowedTransitions() — this
 * action only loads, delegates, and persists.
 */
final readonly class TransitionInquiryStatus
{
    public function __construct(private InquiryRepository $inquiries) {}

    /** @throws DomainException when the inquiry does not exist or the transition is illegal. */
    public function __invoke(string $reference, InquiryStatus $target): ConfidentialInquiry
    {
        $inquiry = $this->inquiries->findByReference(InquiryReference::fromString($reference));

        if ($inquiry === null) {
            throw new DomainException(sprintf('No inquiry exists with reference "%s".', $reference));
        }

        $inquiry->transitionTo($target);

        $this->inquiries->save($inquiry);

        return $inquiry;
    }
}
