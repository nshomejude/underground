<?php

declare(strict_types=1);

namespace Application\Membership\Actions;

use DateTimeImmutable;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\Repositories\MembershipApplicationRepository;
use Domain\Membership\ValueObjects\MemberId;

/**
 * Approves a membership application and issues its permanent member id in
 * the same operation. The next sequence number is the one piece of the
 * card credential only storage can answer, so it is resolved here — one
 * layer above the aggregate — and handed to MembershipApplication::approve()
 * to assign.
 *
 * There is no staff-facing approval UI yet (out of scope for this module);
 * this action exists so approval — wherever it is eventually triggered from
 * — always goes through one path, and so the demo seeder can exercise the
 * real domain transition rather than writing the status column directly.
 */
final readonly class ApproveMembershipApplication
{
    public function __construct(private MembershipApplicationRepository $applications) {}

    public function __invoke(MembershipApplication $application, ?DateTimeImmutable $issuedOn = null): MembershipApplication
    {
        $issuedOn ??= new DateTimeImmutable;

        $memberId = MemberId::assign(
            year: (int) $issuedOn->format('Y'),
            sequence: $this->applications->nextMemberIdSequence(),
        );

        $application->approve($memberId);

        $this->applications->save($application);

        return $application;
    }
}
