<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\PartnerCategory;
use Domain\Content\Repositories\PartnerCategoryRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\PartnerCategoryRecord;

final class EloquentPartnerCategoryRepository implements PartnerCategoryRepository
{
    public function all(): array
    {
        return PartnerCategoryRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?PartnerCategory
    {
        $record = PartnerCategoryRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(PartnerCategory $category, ?Slug $originalSlug = null): void
    {
        PartnerCategoryRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $category->slug)->value],
            [
                'slug' => $category->slug->value,
                'icon' => $category->icon,
                'title' => $category->title,
                'body' => $category->body,
                'position' => $category->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        PartnerCategoryRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(PartnerCategoryRecord $record): PartnerCategory
    {
        return new PartnerCategory(
            slug: Slug::fromString($record->slug),
            icon: $record->icon,
            title: $record->title,
            body: $record->body,
            position: $record->position,
        );
    }
}
