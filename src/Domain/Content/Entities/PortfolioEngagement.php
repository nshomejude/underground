<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * A selected, anonymised past engagement shown on /portfolio.
 */
final readonly class PortfolioEngagement
{
    public function __construct(
        public Slug $slug,
        public string $sector,
        public string $title,
        public string $summary,
        public string $outcome,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'sector' => $this->sector,
            'title' => $this->title,
            'summary' => $this->summary,
            'outcome' => $this->outcome,
            'position' => $this->position,
        ];
    }
}
