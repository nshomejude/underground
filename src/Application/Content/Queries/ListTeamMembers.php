<?php

declare(strict_types=1);

namespace Application\Content\Queries;

use Domain\Content\Entities\TeamMember;
use Domain\Content\Repositories\TeamMemberRepository;

final readonly class ListTeamMembers
{
    public function __construct(private TeamMemberRepository $members) {}

    /** @return list<TeamMember> */
    public function __invoke(): array
    {
        return $this->members->all();
    }
}
