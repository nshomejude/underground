<?php

declare(strict_types=1);

namespace Domain\Membership\Repositories;

use Domain\Membership\Entities\MembershipTier;
use Domain\Shared\ValueObjects\Slug;

interface MembershipTierRepository
{
    /** @return list<MembershipTier> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?MembershipTier;
}
