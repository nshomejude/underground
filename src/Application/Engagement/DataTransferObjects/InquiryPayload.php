<?php

declare(strict_types=1);

namespace Application\Engagement\DataTransferObjects;

use Domain\Engagement\ValueObjects\InterestArea;
use Domain\Shared\ValueObjects\EmailAddress;

/**
 * Validated, transport-agnostic input for the submit-inquiry use case.
 */
final readonly class InquiryPayload
{
    public function __construct(
        public string $name,
        public ?string $organisation,
        public EmailAddress $email,
        public ?string $phone,
        public ?string $country,
        public InterestArea $interest,
        public string $brief,
    ) {}

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            organisation: isset($data['organisation']) ? (string) $data['organisation'] : null,
            email: EmailAddress::fromString((string) ($data['email'] ?? '')),
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            country: isset($data['country']) ? (string) $data['country'] : null,
            interest: InterestArea::tryFrom((string) ($data['interest'] ?? '')) ?? InterestArea::Undisclosed,
            brief: (string) ($data['brief'] ?? ''),
        );
    }
}
