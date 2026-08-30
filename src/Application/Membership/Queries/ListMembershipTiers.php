<?php

declare(strict_types=1);

// STUB: will be replaced by the real Membership domain module at merge time

namespace Application\Membership\Queries;

use Application\Membership\Support\MembershipStubDirectory;
use Domain\Membership\Entities\MembershipTier;
use Domain\Shared\ValueObjects\Slug;

final readonly class ListMembershipTiers
{
    /** @return list<MembershipTier> */
    public function __invoke(): array
    {
        return array_map(
            static fn (array $tier): MembershipTier => new MembershipTier(
                slug: Slug::fromString($tier['slug']),
                name: $tier['name'],
                audience: $tier['audience'],
                icon: $tier['icon'],
                position: $tier['position'],
            ),
            MembershipStubDirectory::tiers(),
        );
    }

    public function bySlug(string $slug): ?MembershipTier
    {
        foreach (($this)() as $tier) {
            if ($tier->slug->value === $slug) {
                return $tier;
            }
        }

        return null;
    }
}
