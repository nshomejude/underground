<?php

declare(strict_types=1);

namespace Domain\Shared\ValueObjects;

use Domain\Shared\Exceptions\DomainException;
use Stringable;

final readonly class EmailAddress implements Stringable
{
    private function __construct(public string $value) {}

    public static function fromString(string $value): self
    {
        $normalised = strtolower(trim($value));

        if (filter_var($normalised, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException(sprintf('"%s" is not a valid email address.', $value));
        }

        return new self($normalised);
    }

    public function domain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
