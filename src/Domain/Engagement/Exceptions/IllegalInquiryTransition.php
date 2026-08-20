<?php

declare(strict_types=1);

namespace Domain\Engagement\Exceptions;

use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Shared\Exceptions\DomainException;

final class IllegalInquiryTransition extends DomainException
{
    public static function between(InquiryStatus $from, InquiryStatus $to): self
    {
        return new self(sprintf(
            'An inquiry cannot move from "%s" to "%s".',
            $from->value,
            $to->value,
        ));
    }
}
