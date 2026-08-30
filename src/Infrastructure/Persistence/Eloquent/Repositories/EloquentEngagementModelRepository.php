<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\EngagementModel;
use Domain\Content\Repositories\EngagementModelRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\EngagementModelRecord;

final class EloquentEngagementModelRepository implements EngagementModelRepository
{
    public function all(): array
    {
        return EngagementModelRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?EngagementModel
    {
        $record = EngagementModelRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(EngagementModel $model, ?Slug $originalSlug = null): void
    {
        EngagementModelRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $model->slug)->value],
            [
                'slug' => $model->slug->value,
                'name' => $model->name,
                'summary' => $model->summary,
                'icon' => $model->icon,
                'position' => $model->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        EngagementModelRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(EngagementModelRecord $record): EngagementModel
    {
        return new EngagementModel(
            slug: Slug::fromString($record->slug),
            name: $record->name,
            summary: $record->summary,
            icon: $record->icon,
            position: $record->position,
        );
    }
}
