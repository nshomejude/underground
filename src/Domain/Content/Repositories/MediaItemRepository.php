<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\MediaItem;
use Domain\Shared\ValueObjects\Slug;

interface MediaItemRepository
{
    /** @return list<MediaItem> ordered by type then position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?MediaItem;

    /**
     * Create or update a media item. When $originalSlug is given, the
     * record matching it is updated in place (allowing the slug itself to
     * change); otherwise the item's own slug is used, creating a new row
     * if none matches it yet.
     */
    public function save(MediaItem $item, ?Slug $originalSlug = null): void;

    public function delete(Slug $slug): void;
}
