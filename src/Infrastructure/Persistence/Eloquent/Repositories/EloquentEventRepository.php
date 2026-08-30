<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\Event;
use Domain\Content\Repositories\EventRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Support\Carbon;
use Infrastructure\Persistence\Eloquent\Models\EventRecord;

final class EloquentEventRepository implements EventRepository
{
    public function all(): array
    {
        return EventRecord::query()
            ->orderBy('date')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?Event
    {
        $record = EventRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(Event $event, ?Slug $originalSlug = null): void
    {
        EventRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $event->slug)->value],
            [
                'slug' => $event->slug->value,
                'name' => $event->name,
                'date' => $event->date->toDateString(),
                'location' => $event->location,
                'description' => $event->description,
                'position' => $event->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        EventRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(EventRecord $record): Event
    {
        return new Event(
            slug: Slug::fromString($record->slug),
            name: $record->name,
            date: Carbon::parse($record->date),
            location: $record->location,
            description: $record->description,
            position: $record->position,
        );
    }
}
