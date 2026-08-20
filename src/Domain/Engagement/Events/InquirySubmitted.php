<?php

declare(strict_types=1);

namespace Domain\Engagement\Events;

use Domain\Engagement\Entities\ConfidentialInquiry;

/**
 * Raised once an inquiry is durably stored. Listeners route it — the brief
 * itself never leaves the aggregate.
 */
final readonly class InquirySubmitted
{
    public function __construct(public ConfidentialInquiry $inquiry)
    {
    }
}
