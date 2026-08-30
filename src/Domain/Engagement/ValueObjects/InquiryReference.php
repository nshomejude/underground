<?php

declare(strict_types=1);

namespace Domain\Engagement\ValueObjects;

use Domain\Shared\Exceptions\DomainException;
use Stringable;

/**
 * The only handle a prospective client is given for a confidential inquiry.
 * Deliberately opaque and non-sequential — nothing about it reveals volume.
 *
 * Shape: UG-2026-7KQ4XB
 */
final readonly class InquiryReference implements Stringable
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private const PATTERN = '/^UG-\d{4}-[A-HJ-NP-Z2-9]{6}$/';

    private function __construct(public string $value) {}

    public static function fromString(string $value): self
    {
        $normalised = strtoupper(trim($value));

        if (preg_match(self::PATTERN, $normalised) !== 1) {
            throw new DomainException(sprintf('"%s" is not a valid inquiry reference.', $value));
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

        return new self(sprintf('UG-%04d-%s', $year, $suffix));
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
