<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\Sector;
use Domain\Content\Repositories\SectorRepository;
use Domain\Shared\ValueObjects\Slug;

final readonly class ListSectors
{
    public function __construct(private SectorRepository $sectors)
    {
    }

    /** @return list<Sector> */
    public function __invoke(): array
    {
        return $this->sectors->all();
    }

    public function bySlug(string $slug): ?Sector
    {
        return $this->sectors->findBySlug(Slug::fromString($slug));
    }
}
