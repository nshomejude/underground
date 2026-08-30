<?php

declare(strict_types=1);

namespace Tests\Unit\Shared;

use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\EmailAddress;
use Tests\TestCase;

final class EmailAddressTest extends TestCase
{
    public function test_from_string_normalises_case_and_whitespace(): void
    {
        $email = EmailAddress::fromString('  Jordan@Achebe-Holdings.EXAMPLE  ');

        $this->assertSame('jordan@achebe-holdings.example', $email->value);
        $this->assertSame('jordan@achebe-holdings.example', (string) $email);
    }

    public function test_from_string_rejects_an_invalid_address(): void
    {
        $this->expectException(DomainException::class);

        EmailAddress::fromString('not-an-email');
    }

    public function test_domain_returns_the_part_after_the_at_sign(): void
    {
        $email = EmailAddress::fromString('partner@state.gov');

        $this->assertSame('state.gov', $email->domain());
    }
}
