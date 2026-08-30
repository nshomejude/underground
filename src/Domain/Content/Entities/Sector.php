<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * A vertical Underground operates in. Rendered as an image tile in the
 * "Sectors we serve" rail.
 */
final readonly class Sector
{
    public function __construct(
        public Slug $slug,
        public string $name,
        public string $summary,
        public string $motif,
        public int $position,
    ) {}

    /** The tile caption wraps onto two lines at the ampersand. */
    public function nameLines(): array
    {
        $parts = explode(' & ', $this->name, 2);

        return count($parts) === 2
            ? [$parts[0].' &', $parts[1]]
            : [$this->name];
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'name' => $this->name,
            'name_lines' => $this->nameLines(),
            'summary' => $this->summary,
            'motif' => $this->motif,
            'position' => $this->position,
        ];
    }
}
