<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Content\ValueObjects\MediaItemStatus;
use Domain\Content\ValueObjects\MediaType;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Support\Carbon;

/**
 * A single piece of Flex Media Group content — a livestream briefing, a
 * podcast episode, or an Underground Magazine issue — shown on
 * /flex-media-group. Status is stored explicitly (Upcoming | Published |
 * Archived) rather than derived from the date, so a future-dated
 * announcement and a dated back issue both render sensibly.
 */
final readonly class MediaItem
{
    public function __construct(
        public Slug $slug,
        public MediaType $type,
        public string $title,
        public string $description,
        public ?Carbon $date,
        public ?string $speaker,
        public MediaItemStatus $status,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'type' => $this->type->value,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'speaker' => $this->speaker,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'position' => $this->position,
        ];
    }
}
