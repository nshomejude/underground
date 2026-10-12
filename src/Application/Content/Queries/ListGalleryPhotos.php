<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\GalleryPhoto;
use Domain\Content\Repositories\GalleryPhotoRepository;

final readonly class ListGalleryPhotos
{
    public function __construct(private GalleryPhotoRepository $photos) {}

    /** @return list<GalleryPhoto> */
    public function __invoke(): array
    {
        return $this->photos->all();
    }
}
