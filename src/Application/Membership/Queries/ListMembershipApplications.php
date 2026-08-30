<?php

declare(strict_types=1);

namespace Application\Membership\Queries;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Repositories\MembershipApplicationRepository;

/** Backs the staff review queue: every membership application, newest first. */
final readonly class ListMembershipApplications
{
    public function __construct(private MembershipApplicationRepository $applications) {}

    /** @return list<MembershipApplication> */
    public function __invoke(): array
    {
        return $this->applications->all();
    }
}
