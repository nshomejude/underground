<?php

declare(strict_types=1);

// STUB: will be replaced by the real Membership domain module at merge time

namespace Domain\Membership\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * One of the vetted tiers Underground extends to governments, principals,
 * and corporate institutions: "Sovereign Partner", "Principal Circle",
 * "Corporate Affiliate". There is no public checkout — see
 * MembershipApplication for the review flow.
 */
final readonly class MembershipTier
{
    public function __construct(
        public Slug $slug,
        public string $name,
        public string $audience,
        public string $icon,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'name' => $this->name,
            'audience' => $this->audience,
            'icon' => $this->icon,
            'position' => $this->position,
        ];
    }
}
