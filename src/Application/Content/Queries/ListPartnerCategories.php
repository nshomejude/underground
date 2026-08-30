<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\PartnerCategory;
use Domain\Content\Repositories\PartnerCategoryRepository;

final readonly class ListPartnerCategories
{
    public function __construct(private PartnerCategoryRepository $categories) {}

    /** @return list<PartnerCategory> */
    public function __invoke(): array
    {
        return $this->categories->all();
    }
}
