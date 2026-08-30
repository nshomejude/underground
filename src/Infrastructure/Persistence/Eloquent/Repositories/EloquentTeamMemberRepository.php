<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\TeamMember;
use Domain\Content\Repositories\TeamMemberRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\TeamMemberRecord;

final class EloquentTeamMemberRepository implements TeamMemberRepository
{
    public function all(): array
    {
        return TeamMemberRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?TeamMember
    {
        $record = TeamMemberRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(TeamMember $member, ?Slug $originalSlug = null): void
    {
        TeamMemberRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $member->slug)->value],
            [
                'slug' => $member->slug->value,
                'name' => $member->name,
                'title' => $member->title,
                'background' => $member->background,
                'icon' => $member->icon,
                'has_portrait' => $member->hasPortrait,
                'position' => $member->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        TeamMemberRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(TeamMemberRecord $record): TeamMember
    {
        return new TeamMember(
            slug: Slug::fromString($record->slug),
            name: $record->name,
            title: $record->title,
            background: $record->background,
            icon: $record->icon,
            hasPortrait: (bool) $record->has_portrait,
            position: $record->position,
        );
    }
}
