<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\Capability;
use Domain\Content\Repositories\CapabilityRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;

final class EloquentCapabilityRepository implements CapabilityRepository
{
    public function all(): array
    {
        return CapabilityRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function featured(?int $limit = null): array
    {
        return CapabilityRecord::query()
            ->where('is_featured', true)
            ->orderBy('position')
            ->when($limit !== null, fn ($query) => $query->limit($limit))
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?Capability
    {
        $record = CapabilityRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    private function toEntity(CapabilityRecord $record): Capability
    {
        return new Capability(
            slug: Slug::fromString($record->slug),
            title: $record->title,
            summary: $record->summary,
            icon: $record->icon,
            position: $record->position,
            isFeatured: $record->is_featured,
        );
    }
}
