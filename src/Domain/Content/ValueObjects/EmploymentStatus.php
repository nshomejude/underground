<?php

declare(strict_types=1);

namespace Domain\Content\ValueObjects;

/**
 * Whether a team member is engaged directly by the firm or brought in on
 * an independent/affiliated basis. Shown on the team roster so the firm
 * never implies a staffing relationship that does not exist.
 */
enum EmploymentStatus: string
{
    case Employee = 'employee';
    case Independent = 'independent';

    public function label(): string
    {
        return match ($this) {
            self::Employee => 'Employee',
            self::Independent => 'Independent',
        };
    }
}
