<?php

declare(strict_types=1);

namespace Domain\Engagement\Repositories;

use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\ValueObjects\InquiryReference;

interface InquiryRepository
{
    public function save(ConfidentialInquiry $inquiry): void;

    public function findByReference(InquiryReference $reference): ?ConfidentialInquiry;

    /** Every inquiry, most recently submitted first — backs the staff review queue. */
    public function all(): array;
}
