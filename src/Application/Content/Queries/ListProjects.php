<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\Project;
use Domain\Content\Repositories\ProjectRepository;

final readonly class ListProjects
{
    public function __construct(private ProjectRepository $projects) {}

    /** @return list<Project> */
    public function __invoke(): array
    {
        return $this->projects->all();
    }
}
