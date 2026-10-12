<?php

declare(strict_types=1);

namespace Domain\Content\ValueObjects;

/**
 * Where a media item sits in its lifecycle. Stored explicitly (rather than
 * derived from its date, as Event derives past/upcoming) because a
 * Magazine issue or an Archived recording can predate today without being
 * "upcoming", and an item can be announced well ahead of its date.
 */
enum MediaItemStatus: string
{
    case Upcoming = 'upcoming';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Upcoming => 'Upcoming',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }
}
