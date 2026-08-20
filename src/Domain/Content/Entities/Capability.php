<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * One of the disciplines Underground sells: "Government & Political Affairs",
 * "Strategic Intelligence & Analysis", and so on.
 */
final readonly class Capability
{
    public function __construct(
        public Slug $slug,
        public string $title,
        public string $summary,
        public string $icon,
        public int $position,
        public bool $isFeatured,
    ) {
    }

    /**
     * The mobile layout shows the title on a single line, the desktop card
     * breaks it across two. The ampersand is the natural hinge.
     */
    public function titleLines(): array
    {
        $parts = explode(' & ', $this->title, 2);

        return count($parts) === 2
            ? [$parts[0].' &', $parts[1]]
            : [$this->title];
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'title' => $this->title,
            'title_lines' => $this->titleLines(),
            'summary' => $this->summary,
            'icon' => $this->icon,
            'position' => $this->position,
            'featured' => $this->isFeatured,
        ];
    }
}
