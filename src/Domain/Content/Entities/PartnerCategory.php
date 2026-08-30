<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * A category of organisation Underground works alongside, shown on
 * /partners. Types, never names.
 */
final readonly class PartnerCategory
{
    public function __construct(
        public Slug $slug,
        public string $icon,
        public string $title,
        public string $body,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'icon' => $this->icon,
            'title' => $this->title,
            'body' => $this->body,
            'position' => $this->position,
        ];
    }
}
