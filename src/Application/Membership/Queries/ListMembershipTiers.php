<?php

declare(strict_types=1);

namespace Application\Membership\Queries;

use Domain\Membership\Entities\MembershipTier;
use Domain\Membership\Repositories\MembershipTierRepository;
use Domain\Shared\ValueObjects\Slug;

final readonly class ListMembershipTiers
{
    public function __construct(private MembershipTierRepository $tiers) {}

    /** @return list<MembershipTier> */
    public function __invoke(): array
    {
        return $this->tiers->all();
    }

    public function bySlug(string $slug): ?MembershipTier
    {
        return $this->tiers->findBySlug(Slug::fromString($slug));
    }
}
