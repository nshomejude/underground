<?php

declare(strict_types=1);

namespace Application\Engagement\Queries;

use Domain\Engagement\Entities\ConfidentialInquiry;
use Domain\Engagement\Repositories\InquiryRepository;
use Domain\Engagement\ValueObjects\InquiryReference;

final readonly class TrackInquiry
{
    public function __construct(private InquiryRepository $inquiries) {}

    public function __invoke(string $reference): ?ConfidentialInquiry
    {
        return $this->inquiries->findByReference(
            InquiryReference::fromString($reference),
        );
    }
}
