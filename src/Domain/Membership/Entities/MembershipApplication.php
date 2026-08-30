<?php

declare(strict_types=1);

// STUB: will be replaced by the real Membership domain module at merge time

namespace Domain\Membership\Entities;

use DateTimeImmutable;
use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;

/**
 * The aggregate root of the Membership context: an approach made through the
 * membership application form for a given tier. Mirrors
 * Domain\Engagement\Entities\ConfidentialInquiry's status-machine shape:
 * submitted -> under_review -> approved / declined.
 */
final class MembershipApplication
{
    private const MINIMUM_STATEMENT_LENGTH = 40;

    private const REFERENCE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private function __construct(
        public readonly string $reference,
        public readonly Slug $tier,
        public readonly string $name,
        public readonly ?string $organisation,
        public readonly EmailAddress $email,
        public readonly ?string $phone,
        public readonly ?string $country,
        public readonly string $statement,
        public readonly DateTimeImmutable $submittedAt,
        private string $status,
    ) {}

    public static function submit(
        Slug $tier,
        string $name,
        ?string $organisation,
        EmailAddress $email,
        ?string $phone,
        ?string $country,
        string $statement,
        ?DateTimeImmutable $submittedAt = null,
        ?string $reference = null,
    ): self {
        $name = trim($name);
        $statement = trim($statement);

        if ($name === '') {
            throw new DomainException('A membership application must carry a name.');
        }

        if (mb_strlen($statement) < self::MINIMUM_STATEMENT_LENGTH) {
            throw new DomainException(sprintf(
                'A statement must be at least %d characters so it can be reviewed.',
                self::MINIMUM_STATEMENT_LENGTH,
            ));
        }

        $submittedAt ??= new DateTimeImmutable;

        return new self(
            reference: $reference ?? self::generateReference((int) $submittedAt->format('Y')),
            tier: $tier,
            name: $name,
            organisation: self::nullIfBlank($organisation),
            email: $email,
            phone: self::nullIfBlank($phone),
            country: self::nullIfBlank($country),
            statement: $statement,
            submittedAt: $submittedAt,
            status: 'submitted',
        );
    }

    public function status(): string
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'reference' => $this->reference,
            'tier' => $this->tier->value,
            'name' => $this->name,
            'organisation' => $this->organisation,
            'email' => $this->email->value,
            'phone' => $this->phone,
            'country' => $this->country,
            'statement' => $this->statement,
            'status' => $this->status,
            'submitted_at' => $this->submittedAt->format(DATE_ATOM),
        ];
    }

    private static function generateReference(int $year): string
    {
        $suffix = '';
        $max = strlen(self::REFERENCE_ALPHABET) - 1;

        for ($i = 0; $i < 6; $i++) {
            $suffix .= self::REFERENCE_ALPHABET[random_int(0, $max)];
        }

        return sprintf('UGM-%04d-%s', $year, $suffix);
    }

    private static function nullIfBlank(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === '' ? null : $value;
    }
}
