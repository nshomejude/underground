<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * A named principal shown on the /team page. The founder gets a real
 * portrait (icon is ignored when hasPortrait is true); every other
 * principal is rendered with an icon avatar instead.
 */
final readonly class TeamMember
{
    public function __construct(
        public Slug $slug,
        public string $name,
        public string $title,
        public string $background,
        public ?string $icon,
        public bool $hasPortrait,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'name' => $this->name,
            'title' => $this->title,
            'background' => $this->background,
            'icon' => $this->icon,
            'portrait' => $this->hasPortrait,
            'position' => $this->position,
        ];
    }
}
