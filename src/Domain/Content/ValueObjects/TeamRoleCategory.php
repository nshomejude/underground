<?php

declare(strict_types=1);

namespace Domain\Content\ValueObjects;

/**
 * Separates GOVERNANCE from OPERATIONAL standing on the team roster.
 *
 * - Governance: Board Members, Advisory Board — oversight and counsel,
 *   not day-to-day delivery.
 * - Executive: President, CEO and other confirmed C-level officers.
 * - Regional: Regional President/Managing Director, Vice President,
 *   Country Manager, Representative — geographic leadership.
 * - Expertise: Senior Partner, Partner, Principal, Director, Manager,
 *   Consultant, Analyst, Field Representative — the delivery bench.
 */
enum TeamRoleCategory: string
{
    case Governance = 'governance';
    case Executive = 'executive';
    case Regional = 'regional';
    case Expertise = 'expertise';

    public function label(): string
    {
        return match ($this) {
            self::Governance => 'Governance',
            self::Executive => 'Executive',
            self::Regional => 'Regional',
            self::Expertise => 'Expertise',
        };
    }
}
