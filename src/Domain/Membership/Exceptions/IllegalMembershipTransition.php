<?php

declare(strict_types=1);

namespace Domain\Membership\Exceptions;

use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\Exceptions\DomainException;

final class IllegalMembershipTransition extends DomainException
{
    public static function between(MembershipApplicationStatus $from, MembershipApplicationStatus $to): self
    {
        return new self(sprintf(
            'A membership application cannot move from "%s" to "%s".',
            $from->value,
            $to->value,
        ));
    }
}
