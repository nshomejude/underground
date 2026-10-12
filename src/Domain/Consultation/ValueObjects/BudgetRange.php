<?php

declare(strict_types=1);

namespace Domain\Consultation\ValueObjects;

/**
 * An indicative budget bracket, not a quote. Bracketed rather than a free
 * figure so the form never records (or implies) a confirmed price, and a
 * requester who would rather not say still has a legitimate option.
 */
enum BudgetRange: string
{
    case Under25k = 'under-25k';
    case From25kTo100k = '25k-100k';
    case From100kTo500k = '100k-500k';
    case Over500k = 'over-500k';
    case Undisclosed = 'undisclosed';

    public function label(): string
    {
        return match ($this) {
            self::Under25k => 'Under $25,000',
            self::From25kTo100k => '$25,000 – $100,000',
            self::From100kTo500k => '$100,000 – $500,000',
            self::Over500k => 'Over $500,000',
            self::Undisclosed => 'Prefer not to say',
        };
    }
}
