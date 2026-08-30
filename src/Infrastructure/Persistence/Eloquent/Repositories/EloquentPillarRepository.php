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
