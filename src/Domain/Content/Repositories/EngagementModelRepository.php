<?php

declare(strict_types=1);

namespace Domain\Content\Repositories;

use Domain\Content\Entities\EngagementModel;
use Domain\Shared\ValueObjects\Slug;

interface EngagementModelRepository
{
    /** @return list<EngagementModel> ordered by position */
    public function all(): array;

    public function findBySlug(Slug $slug): ?EngagementModel;
}
