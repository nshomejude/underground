<?php

declare(strict_types=1);

namespace Domain\Consultation\Exceptions;

use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Shared\Exceptions\DomainException;

final class IllegalConsultationTransition extends DomainException
{
    public static function between(ConsultationStatus $from, ConsultationStatus $to): self
    {
        return new self(sprintf(
            'A consultation request cannot move from "%s" to "%s".',
            $from->value,
            $to->value,
        ));
    }
}
