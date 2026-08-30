<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * A current, ongoing initiative shown on /projects — distinct from
 * Portfolio, which is closed, past work.
 */
final readonly class Project
{
    public function __construct(
        public Slug $slug,
        public string $icon,
        public string $title,
        public string $sector,
        public string $body,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'icon' => $this->icon,
            'title' => $this->title,
            'sector' => $this->sector,
            'body' => $this->body,
            'position' => $this->position,
        ];
    }
}
