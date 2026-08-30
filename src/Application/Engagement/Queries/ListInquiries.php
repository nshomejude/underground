<?php

declare(strict_types=1);

namespace Application\Engagement\Queries;

use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\Repositories\InquiryRepository;

/** Backs the staff review queue: every confidential inquiry, newest first. */
final readonly class ListInquiries
{
    public function __construct(private InquiryRepository $inquiries) {}

    /** @return list<ConfidentialInquiry> */
    public function __invoke(): array
    {
        return $this->inquiries->all();
    }
}
