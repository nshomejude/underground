<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Membership\Entities\MembershipTier;
use Domain\Membership\Repositories\MembershipTierRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;

final class EloquentMembershipTierRepository implements MembershipTierRepository
{
    public function all(): array
    {
        return MembershipTierRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?MembershipTier
    {
        $record = MembershipTierRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(MembershipTier $tier, ?Slug $originalSlug = null): void
    {
        MembershipTierRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $tier->slug)->value],
            [
                'slug' => $tier->slug->value,
                'name' => $tier->name,
                'audience' => $tier->audience,
                'icon' => $tier->icon,
                'position' => $tier->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        MembershipTierRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(MembershipTierRecord $record): MembershipTier
    {
        return new MembershipTier(
            slug: Slug::fromString($record->slug),
            name: $record->name,
            audience: $record->audience,
            icon: $record->icon,
            position: $record->position,
        );
    }
}
