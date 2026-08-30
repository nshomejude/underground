<?php

declare(strict_types=1);

namespace Domain\Membership\Repositories;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MembershipReference;
use Domain\Shared\ValueObjects\EmailAddress;

interface MembershipApplicationRepository
{
    public function save(MembershipApplication $application): void;

    public function findByReference(MembershipReference $reference): ?MembershipApplication;

    /** The most recently submitted application for this email, matched case-insensitively. */
    public function findByEmail(EmailAddress $email): ?MembershipApplication;

    /**
     * The sequence number the next approved application should be issued —
     * one greater than however many member ids have been assigned so far.
     */
    public function nextMemberIdSequence(): int;

    /** Every application, most recently submitted first — backs the staff review queue. */
    public function all(): array;
}
