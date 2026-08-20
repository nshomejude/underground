<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\Sector;
use Domain\Content\Repositories\SectorRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\SectorRecord;

final class EloquentSectorRepository implements SectorRepository
{
    public function all(): array
    {
        return SectorRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?Sector
    {
        $record = SectorRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    private function toEntity(SectorRecord $record): Sector
    {
        return new Sector(
            slug: Slug::fromString($record->slug),
            name: $record->name,
            summary: $record->summary,
            motif: $record->motif,
            position: $record->position,
        );
    }
}
