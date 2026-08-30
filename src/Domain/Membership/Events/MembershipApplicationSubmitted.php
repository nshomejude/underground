<?php

declare(strict_types=1);

namespace Domain\Membership\Events;

use Domain\Membership\Entities\MembershipApplication;

/**
 * Raised once a membership application is durably stored. Listeners route
 * it — the statement itself never leaves the aggregate.
 */
final readonly class MembershipApplicationSubmitted
{
    public function __construct(public MembershipApplication $application) {}
}
