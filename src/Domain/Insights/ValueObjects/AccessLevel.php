<?php

declare(strict_types=1);

namespace Domain\Insights\ValueObjects;

/**
 * Who may read a report in full. Lot 1 only models and displays this (the
 * "Member Access" badge on members-only reports) -- it does not enforce an
 * actual paywall. That enforcement is left to a later lot.
 */
enum AccessLevel: string
{
    case PublicAccess = 'public';
    case MembersOnly = 'members';

    public function label(): string
    {
        return match ($this) {
            self::PublicAccess => 'Public',
            self::MembersOnly => 'Member Access',
        };
    }

    public function requiresMembership(): bool
    {
        return $this === self::MembersOnly;
    }
}
