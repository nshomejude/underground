<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\Office;
use Domain\Content\Repositories\OfficeRepository;
use Domain\Shared\ValueObjects\Slug;

final readonly class ListOffices
{
    public function __construct(private OfficeRepository $offices) {}

    /** @return list<Office> */
    public function __invoke(): array
    {
        return $this->offices->all();
    }

    public function bySlug(string $slug): ?Office
    {
        return $this->offices->findBySlug(Slug::fromString($slug));
    }
}
