<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\EngagementModel;
use Domain\Content\Repositories\EngagementModelRepository;
use Domain\Shared\ValueObjects\Slug;

final readonly class ListEngagementModels
{
    public function __construct(private EngagementModelRepository $models) {}

    /** @return list<EngagementModel> */
    public function __invoke(): array
    {
        return $this->models->all();
    }

    public function bySlug(string $slug): ?EngagementModel
    {
        return $this->models->findBySlug(Slug::fromString($slug));
    }
}
