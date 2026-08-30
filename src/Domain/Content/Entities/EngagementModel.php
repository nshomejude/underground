<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * How a client can retain the firm — the four rows in the
 * "Engagement models" panel beside the global reach map.
 */
final readonly class EngagementModel
{
    public function __construct(
        public Slug $slug,
        public string $name,
        public string $summary,
        public string $icon,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'name' => $this->name,
            'summary' => $this->summary,
            'icon' => $this->icon,
            'position' => $this->position,
        ];
    }
}
