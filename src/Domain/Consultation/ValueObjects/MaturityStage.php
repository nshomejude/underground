<?php

declare(strict_types=1);

namespace Domain\Consultation\ValueObjects;

/**
 * How far along the requester's project is — helps staff judge how much
 * qualification a request needs before any paid consultation is offered.
 */
enum MaturityStage: string
{
    case Idea = 'idea';
    case EarlyStage = 'early-stage';
    case Growth = 'growth';
    case Established = 'established';

    public function label(): string
    {
        return match ($this) {
            self::Idea => 'Idea / Concept',
            self::EarlyStage => 'Early-Stage',
            self::Growth => 'Growth-Stage',
            self::Established => 'Established / Mature',
        };
    }
}
