<?php

declare(strict_types=1);

namespace Domain\Membership\Repositories;

use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MembershipReference;

interface MembershipApplicationRepository
{
    public function save(MembershipApplication $application): void;

    public function findByReference(MembershipReference $reference): ?MembershipApplication;
}
