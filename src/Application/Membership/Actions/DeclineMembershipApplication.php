<?php

declare(strict_types=1);

namespace Application\Membership\Actions;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Repositories\MembershipApplicationRepository;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;

/**
 * Declines a membership application. Mirrors ApproveMembershipApplication's
 * shape but carries no member id to assign — a declined application never
 * receives one.
 */
final readonly class DeclineMembershipApplication
{
    public function __construct(private MembershipApplicationRepository $applications) {}

    public function __invoke(MembershipApplication $application): MembershipApplication
    {
        $application->transitionTo(MembershipApplicationStatus::Declined);

        $this->applications->save($application);

        return $application;
    }
}
