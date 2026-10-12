<?php

declare(strict_types=1);

namespace Domain\Content\ValueObjects;

/**
 * How present Underground actually is in a given market. This is the
 * single source of truth that keeps the Global Reach / Global Alliance /
 * Locations pages honest: a location is never shown as more than this
 * status says it is, and only ActiveOffice means Underground-staffed.
 */
enum OfficeStatus: string
{
    case Planned = 'planned';
    case PartnerCoverage = 'partner_coverage';
    case RepresentativeOffice = 'representative_office';
    case ActiveOffice = 'active_office';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Planned',
            self::PartnerCoverage => 'Partner Coverage',
            self::RepresentativeOffice => 'Representative Office',
            self::ActiveOffice => 'Active Office',
        };
    }

    /**
     * A short, honest gloss of what this status does — and does not —
     * claim. Surfaced on the transparency pages so a badge is never read
     * as more than it says.
     */
    public function description(): string
    {
        return match ($this) {
            self::Planned => 'Under consideration only. No local presence or partner has been confirmed yet.',
            self::PartnerCoverage => 'Served through an independent partner or associate — not an Underground-staffed office.',
            self::RepresentativeOffice => 'A limited, named local presence maintained on Underground\'s behalf, short of a fully staffed branch.',
            self::ActiveOffice => 'A directly operated Underground office, staffed by Underground personnel.',
        };
    }
}
