<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\Office;
use Domain\Content\Repositories\OfficeRepository;
use Domain\Content\ValueObjects\OfficeStatus;
use Domain\Content\ValueObjects\RelationshipType;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\OfficeRecord;

final class EloquentOfficeRepository implements OfficeRepository
{
    public function all(): array
    {
        return OfficeRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?Office
    {
        $record = OfficeRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(Office $office, ?Slug $originalSlug = null): void
    {
        OfficeRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $office->slug)->value],
            [
                'slug' => $office->slug->value,
                'name' => $office->name,
                'region' => $office->region,
                'address' => $office->address,
                'email' => $office->email,
                'phone' => $office->phone,
                'blurb' => $office->blurb,
                'lat' => $office->lat,
                'lng' => $office->lng,
                'countries' => $office->countries,
                'status' => $office->status->value,
                'relationship_type' => $office->relationshipType->value,
                'is_headquarters' => $office->isHeadquarters,
                'position' => $office->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        OfficeRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(OfficeRecord $record): Office
    {
        return new Office(
            slug: Slug::fromString($record->slug),
            name: $record->name,
            region: $record->region,
            address: $record->address,
            email: $record->email,
            phone: $record->phone,
            blurb: $record->blurb,
            lat: $record->lat,
            lng: $record->lng,
            countries: $record->countries ?? [],
            status: OfficeStatus::from($record->status),
            relationshipType: RelationshipType::from($record->relationship_type),
            isHeadquarters: (bool) $record->is_headquarters,
            position: $record->position,
        );
    }
}
