<?php

declare(strict_types=1);

namespace Domain\Content\ValueObjects;

/**
 * The three formats published under Flex Media Group: livestream
 * briefings, podcast episodes, and Underground Magazine issues.
 */
enum MediaType: string
{
    case Livestream = 'livestream';
    case Podcast = 'podcast';
    case Magazine = 'magazine';

    public function label(): string
    {
        return match ($this) {
            self::Livestream => 'Livestream',
            self::Podcast => 'Podcast',
            self::Magazine => 'Underground Magazine',
        };
    }
}
