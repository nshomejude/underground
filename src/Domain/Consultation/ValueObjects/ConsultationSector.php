<?php

declare(strict_types=1);

namespace Domain\Consultation\ValueObjects;

/**
 * The vertical a consultation request concerns. Mirrors the public Sector
 * catalog's slugs (see Domain\Content\Entities\Sector /
 * database/seeders/SectorSeeder.php) so a request can be routed without a
 * lookup — the same deliberate, self-contained mirroring InterestArea uses
 * for the confidential-inquiry form's practice areas — plus an "Other" case
 * since this intake is open to any prospective client, not only the six
 * verticals the site markets today.
 */
enum ConsultationSector: string
{
    case GovernmentPublicSector = 'government-public-sector';
    case EnergyNaturalResources = 'energy-natural-resources';
    case InfrastructureTransportation = 'infrastructure-transportation';
    case DefenseSecurity = 'defense-security';
    case TechnologyInnovation = 'technology-innovation';
    case FinanceInvestments = 'finance-investments';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::GovernmentPublicSector => 'Government & Public Sector',
            self::EnergyNaturalResources => 'Energy & Natural Resources',
            self::InfrastructureTransportation => 'Infrastructure & Transportation',
            self::DefenseSecurity => 'Defense & Security',
            self::TechnologyInnovation => 'Technology & Innovation',
            self::FinanceInvestments => 'Finance & Investments',
            self::Other => 'Other / Not Listed',
        };
    }
}
