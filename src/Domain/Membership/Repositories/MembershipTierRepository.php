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

    /**
     * Create or update a tier. When $originalSlug is given, the record
     * matching it is updated in place (allowing the slug itself to change);
     * otherwise the tier's own slug is used, creating a new row if none
     * matches it yet.
     */
    public function save(MembershipTier $tier, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
