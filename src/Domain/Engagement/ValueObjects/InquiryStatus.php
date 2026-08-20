<?php

declare(strict_types=1);

namespace Domain\Engagement\ValueObjects;

enum InquiryStatus: string
{
    case Received = 'received';
    case UnderReview = 'under_review';
    case Engaged = 'engaged';
    case Declined = 'declined';
    case Archived = 'archived';

    /** @return list<self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Received => [self::UnderReview, self::Declined, self::Archived],
            self::UnderReview => [self::Engaged, self::Declined, self::Archived],
            self::Engaged, self::Declined => [self::Archived],
            self::Archived => [],
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
            self::Received => 'Received',
            self::UnderReview => 'Under review',
            self::Engaged => 'Engaged',
            self::Declined => 'Declined',
            self::Archived => 'Archived',
        };
    }
}
