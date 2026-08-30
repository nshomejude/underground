<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;
use Illuminate\Support\Carbon;

/**
 * An invitation-only forum the firm convenes or hosts, shown on /events.
 * Past vs upcoming status is derived from the current date rather than
 * stored, so the page stays correct as time passes without an edit.
 */
final readonly class Event
{
    public function __construct(
        public Slug $slug,
        public string $name,
        public Carbon $date,
        public string $location,
        public string $description,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'name' => $this->name,
            'date' => $this->date,
            'location' => $this->location,
            'description' => $this->description,
            'is_past' => $this->date->isPast(),
            'position' => $this->position,
        ];
    }
}
