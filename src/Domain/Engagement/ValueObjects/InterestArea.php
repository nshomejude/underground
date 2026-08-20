<?php

declare(strict_types=1);

namespace Domain\Engagement\ValueObjects;

/**
 * The practice a prospective client is knocking on. Mirrors the capability
 * slugs so an inquiry can be routed to the right partner without a lookup.
 */
enum InterestArea: string
{
    case GovernmentPoliticalAffairs = 'government-political-affairs';
    case InternationalRelations = 'international-relations-diplomacy';
    case StrategicIntelligence = 'strategic-intelligence-analysis';
    case InvestmentCapitalStrategy = 'investment-capital-strategy';
    case BusinessDevelopment = 'business-development-partnerships';
    case MediaNarrative = 'media-narrative-management';
    case InfrastructureAdvisory = 'ppp-infrastructure-advisory';
    case ThinkTankAdvisory = 'think-tank-strategic-advisory';
    case CrisisSpecialSituations = 'crisis-special-situations';
    case Undisclosed = 'undisclosed';

    public function label(): string
    {
        return match ($this) {
            self::GovernmentPoliticalAffairs => 'Government & Political Affairs',
            self::InternationalRelations => 'International Relations & Diplomacy',
            self::StrategicIntelligence => 'Strategic Intelligence & Analysis',
            self::InvestmentCapitalStrategy => 'Investment & Capital Strategy',
            self::BusinessDevelopment => 'Business Development & Partnerships',
            self::MediaNarrative => 'Media & Narrative Management',
            self::InfrastructureAdvisory => 'PPP & Infrastructure Advisory',
            self::ThinkTankAdvisory => 'Think Tank & Strategic Advisory',
            self::CrisisSpecialSituations => 'Crisis Management & Special Situations',
            self::Undisclosed => 'Prefer not to say',
        };
    }

    /**
     * Crisis work and anything left undisclosed goes straight to a partner
     * rather than sitting in the normal intake queue.
     */
    public function requiresPartnerTriage(): bool
    {
        return $this === self::CrisisSpecialSituations || $this === self::Undisclosed;
    }
}
