<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Content\Entities\GalleryPhoto;
use Domain\Content\Repositories\GalleryPhotoRepository;
use Domain\Shared\ValueObjects\Slug;
use Infrastructure\Persistence\Eloquent\Models\GalleryPhotoRecord;

final class EloquentGalleryPhotoRepository implements GalleryPhotoRepository
{
    public function all(): array
    {
        return GalleryPhotoRecord::query()
            ->orderBy('position')
            ->get()
            ->map($this->toEntity(...))
            ->all();
    }

    public function findBySlug(Slug $slug): ?GalleryPhoto
    {
        $record = GalleryPhotoRecord::query()->where('slug', $slug->value)->first();

        return $record === null ? null : $this->toEntity($record);
    }

    public function save(GalleryPhoto $photo, ?Slug $originalSlug = null): void
    {
        GalleryPhotoRecord::query()->updateOrCreate(
            ['slug' => ($originalSlug ?? $photo->slug)->value],
            [
                'slug' => $photo->slug->value,
                'caption' => $photo->caption,
                'context' => $photo->context,
                'captured_on' => $photo->capturedOn,
                'photo_rights_note' => $photo->photoRightsNote,
                'image_url' => $photo->imageUrl,
                'position' => $photo->position,
            ],
        );
    }

    public function delete(Slug $slug): void
    {
        GalleryPhotoRecord::query()->where('slug', $slug->value)->delete();
    }

    private function toEntity(GalleryPhotoRecord $record): GalleryPhoto
    {
        return new GalleryPhoto(
            slug: Slug::fromString($record->slug),
            caption: $record->caption,
            context: $record->context,
            capturedOn: $record->captured_on,
            photoRightsNote: $record->photo_rights_note,
            imageUrl: $record->image_url,
            position: $record->position,
        );
    }
}
