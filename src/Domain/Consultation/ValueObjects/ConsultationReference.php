<?php

declare(strict_types=1);

namespace Domain\Consultation\ValueObjects;

use Domain\Shared\Exceptions\DomainException;
use Stringable;

/**
 * The only handle a prospective client is given for a consultation
 * qualification request. Deliberately opaque and non-sequential — nothing
 * about it reveals volume. Distinct prefix from InquiryReference (UG-) and
 * MembershipReference (UGM-) so the three request kinds are never confused
 * at a glance.
 *
 * Shape: UGC-2026-7KQ4XB
 */
final readonly class ConsultationReference implements Stringable
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private const PATTERN = '/^UGC-\d{4}-[A-HJ-NP-Z2-9]{6}$/';

    private function __construct(public string $value) {}

    public static function fromString(string $value): self
    {
        $normalised = strtoupper(trim($value));

        if (preg_match(self::PATTERN, $normalised) !== 1) {
            throw new DomainException(sprintf('"%s" is not a valid consultation reference.', $value));
        }

        return new self($normalised);
    }

    public static function generate(int $year): self
    {
        $suffix = '';
        $max = strlen(self::ALPHABET) - 1;

        for ($i = 0; $i < 6; $i++) {
            $suffix .= self::ALPHABET[random_int(0, $max)];
        }

        return new self(sprintf('UGC-%04d-%s', $year, $suffix));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
