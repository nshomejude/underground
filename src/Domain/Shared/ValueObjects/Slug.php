<?php

declare(strict_types=1);

namespace Domain\Shared\ValueObjects;

use Domain\Shared\Exceptions\DomainException;
use Stringable;

/**
 * A URL safe, immutable identifier for a piece of published content.
 */
final readonly class Slug implements Stringable
{
    private const PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    private function __construct(public string $value) {}

    public static function fromString(string $value): self
    {
        $normalised = trim(strtolower($value));

        if ($normalised === '' || preg_match(self::PATTERN, $normalised) !== 1) {
            throw new DomainException(sprintf('"%s" is not a valid slug.', $value));
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
