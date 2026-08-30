<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * "Discreet by design", "Strategic by nature" — the four brand pillars.
 * They headline the mobile layout and back the hero card on desktop.
 */
final readonly class Pillar
{
    public function __construct(
        public Slug $slug,
        public string $title,
        public string $qualifier,
        public string $icon,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'title' => $this->title,
            'qualifier' => $this->qualifier,
            'icon' => $this->icon,
            'position' => $this->position,
        ];
    }
}
