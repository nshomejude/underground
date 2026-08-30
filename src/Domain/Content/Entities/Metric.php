<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * A proof point in the credibility bar: "250+ government relationships
 * worldwide", "$20B+ in projects & investments supported".
 */
final readonly class Metric
{
    public function __construct(
        public Slug $slug,
        public string $value,
        public string $label,
        public string $icon,
        public int $position,
    ) {}

    /** The bar prints the label across two short lines under the figure. */
    public function labelLines(): array
    {
        return array_values(array_filter(array_map('trim', explode('|', $this->label))));
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'value' => $this->value,
            'label' => implode(' ', $this->labelLines()),
            'label_lines' => $this->labelLines(),
            'icon' => $this->icon,
            'position' => $this->position,
        ];
    }
}
