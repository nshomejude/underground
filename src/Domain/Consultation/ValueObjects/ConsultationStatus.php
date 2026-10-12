<?php

declare(strict_types=1);

namespace Domain\Consultation\ValueObjects;

/**
 * The qualification pipeline a free consultation request moves through.
 * Deliberately shorter-lived than InquiryStatus's "Engaged" — the actual
 * paid booking/calendar flow is a separate, later lot, so "ProposalSent" is
 * this status machine's last non-terminal stop: it means staff have
 * qualified the request and told the requester what paid consultation
 * format (if any) fits, not that a booking has been made.
 */
enum ConsultationStatus: string
{
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Qualified = 'qualified';
    case ProposalSent = 'proposal_sent';
    case Declined = 'declined';

    /** @return list<self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Submitted => [self::UnderReview, self::Declined],
            self::UnderReview => [self::Qualified, self::Declined],
            self::Qualified => [self::ProposalSent, self::Declined],
            self::ProposalSent => [self::Declined],
            self::Declined => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function isTerminal(): bool
    {
        return $this->allowedTransitions() === [];
    }

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Submitted',
            self::UnderReview => 'Under review',
            self::Qualified => 'Qualified',
            self::ProposalSent => 'Proposal sent',
            self::Declined => 'Declined',
        };
    }
}
