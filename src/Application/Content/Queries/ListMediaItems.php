<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\MediaItem;
use Domain\Content\Repositories\MediaItemRepository;

final readonly class ListMediaItems
{
    public function __construct(private MediaItemRepository $mediaItems) {}

    /** @return list<MediaItem> */
    public function __invoke(): array
    {
        return $this->mediaItems->all();
    }
}
