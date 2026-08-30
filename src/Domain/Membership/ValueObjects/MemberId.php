<?php

declare(strict_types=1);

namespace Domain\Membership\ValueObjects;

use Domain\Shared\Exceptions\DomainException;
use Stringable;

/**
 * The permanent credential printed on a member's physical card, distinct
 * from MembershipReference (the opaque application-tracking handle).
 *
 * Unlike the reference, this is not generated at submission time — it is
 * assigned once, at the moment an application is approved, and never
 * changes again even as the card itself is renewed year over year.
 *
 * Shape: UG · 2026 · 000001
 */
final readonly class MemberId implements Stringable
{
    private const PATTERN = '/^UG · \d{4} · \d{6}$/u';

    private function __construct(public string $value) {}

    /**
     * Assign a member id at issuance: the year membership was first granted,
     * and the applicant's place in the sequence of everyone ever issued one.
     */
    public static function assign(int $year, int $sequence): self
    {
        if ($sequence < 1 || $sequence > 999_999) {
            throw new DomainException(sprintf(
                '"%d" is out of range for a 6-digit member id sequence.',
                $sequence,
            ));
        }

        return new self(sprintf('UG · %04d · %06d', $year, $sequence));
    }

    public static function fromString(string $value): self
    {
        $normalised = trim($value);

        if (preg_match(self::PATTERN, $normalised) !== 1) {
            throw new DomainException(sprintf('"%s" is not a valid member id.', $value));
        }

        return new self($normalised);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
