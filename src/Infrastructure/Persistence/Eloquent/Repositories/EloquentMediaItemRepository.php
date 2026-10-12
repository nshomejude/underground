<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\MediaItem;
use Domain\Content\Repositories\MediaItemRepository;
use Domain\Content\ValueObjects\MediaItemStatus;
use Domain\Content\ValueObjects\MediaType;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Support\Carbon;
use Infrastructure\Persistence\Eloquent\Models\MediaItemRecord;

final class EloquentMediaItemRepository implements MediaItemRepository
{
    public function all(): array
    {
        return MediaItemRecord::query()
            ->orderBy('type')
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?MediaItem
    {
        $record = MediaItemRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(MediaItem $item, ?Slug $originalSlug = null): void
    {
        MediaItemRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $item->slug)->value],
            [
                'slug' => $item->slug->value,
                'type' => $item->type->value,
                'title' => $item->title,
                'description' => $item->description,
                'date' => $item->date?->toDateString(),
                'speaker' => $item->speaker,
                'status' => $item->status->value,
                'position' => $item->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        MediaItemRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(MediaItemRecord $record): MediaItem
    {
        return new MediaItem(
            slug: Slug::fromString($record->slug),
            type: MediaType::from($record->type),
            title: $record->title,
            description: $record->description,
            date: $record->date === null ? null : Carbon::parse($record->date),
            speaker: $record->speaker,
            status: MediaItemStatus::from($record->status),
            position: $record->position,
        );
    }
}
