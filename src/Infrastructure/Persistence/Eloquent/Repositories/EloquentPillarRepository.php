<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\Pillar;
use Domain\Content\Repositories\PillarRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\PillarRecord;

final class EloquentPillarRepository implements PillarRepository
{
    public function all(): array
    {
        return PillarRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?Pillar
    {
        $record = PillarRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(Pillar $pillar, ?Slug $originalSlug = null): void
    {
        PillarRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $pillar->slug)->value],
            [
                'slug' => $pillar->slug->value,
                'title' => $pillar->title,
                'qualifier' => $pillar->qualifier,
                'icon' => $pillar->icon,
                'position' => $pillar->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        PillarRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(PillarRecord $record): Pillar
    {
        return new Pillar(
            slug: Slug::fromString($record->slug),
            title: $record->title,
            qualifier: $record->qualifier,
            icon: $record->icon,
            position: $record->position,
        );
    }
}
