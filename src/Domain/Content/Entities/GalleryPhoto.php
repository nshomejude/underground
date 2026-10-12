<?php

declare(strict_types=1);

namespace Domain\Content\Entities;

use Domain\Shared\ValueObjects\Slug;

/**
 * A single photo shown on /team/gallery. There are no real photos to
 * publish yet (see GalleryPhotoSeeder), so this entity exists to let an
 * admin add genuine ones later rather than to hold stock-photo-style
 * placeholders — imageUrl is nullable and the gallery page is built to
 * render gracefully with it empty (see team/gallery.blade.php).
 *
 * photoRightsNote is required precisely because a photo is never
 * published here without a stated basis for using it.
 */
final readonly class GalleryPhoto
{
    public function __construct(
        public Slug $slug,
        public string $caption,
        public ?string $context,
        public ?string $capturedOn,
        public string $photoRightsNote,
        public ?string $imageUrl,
        public int $position,
    ) {}

    public function toArray(): array
    {
        return [
            'slug' => $this->slug->value,
            'caption' => $this->caption,
            'context' => $this->context,
            'captured_on' => $this->capturedOn,
            'photo_rights_note' => $this->photoRightsNote,
            'image_url' => $this->imageUrl,
            'position' => $this->position,
        ];
    }
}
