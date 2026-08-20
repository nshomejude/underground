<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\Capability;
use Domain\Shared\ValueObjects\Slug;

interface CapabilityRepository
{
    /** @return list<Capability> ordered by position */
    public function all(): array;

    /** @return list<Capability> the subset promoted to the mobile summary list */
    public function featured(?int $limit = null): array;

    public function findBySlug(Slug $slug): ?Capability;
}
