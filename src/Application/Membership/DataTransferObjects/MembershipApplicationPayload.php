<?php

declare(strict_types=1);

// STUB: will be replaced by the real Membership domain module at merge time

namespace Application\Membership\DataTransferObjects;

use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;

/**
 * Validated, transport-agnostic input for the apply-for-membership use case.
 */
final readonly class MembershipApplicationPayload
{
    public function __construct(
        public Slug $tier,
        public string $name,
        public ?string $organisation,
        public EmailAddress $email,
        public ?string $phone,
        public ?string $country,
        public string $statement,
    ) {}

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            tier: Slug::fromString((string) ($data['tier'] ?? '')),
            name: (string) ($data['name'] ?? ''),
            organisation: isset($data['organisation']) ? (string) $data['organisation'] : null,
            email: EmailAddress::fromString((string) ($data['email'] ?? '')),
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            country: isset($data['country']) ? (string) $data['country'] : null,
            statement: (string) ($data['statement'] ?? ''),
        );
    }
}
