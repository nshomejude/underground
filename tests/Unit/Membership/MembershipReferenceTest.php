<?php

declare(strict_types=1);

namespace Tests\Unit\Membership;

use Domain\Membership\ValueObjects\MembershipReference;
use Domain\Shared\Exceptions\DomainException;
use Tests\TestCase;

final class MembershipReferenceTest extends TestCase
{
    public function test_generate_produces_the_expected_shape(): void
    {
        $reference = MembershipReference::generate(2026);

        $this->assertMatchesRegularExpression('/^UGM-2026-[A-HJ-NP-Z2-9]{6}$/', $reference->value);
    }

    public function test_from_string_normalises_case_and_whitespace(): void
    {
        $reference = MembershipReference::fromString('  ugm-2026-7kq4xb  ');

        $this->assertSame('UGM-2026-7KQ4XB', $reference->value);
    }

    public function test_from_string_rejects_an_invalid_value(): void
    {
        $this->expectException(DomainException::class);

        MembershipReference::fromString('UG-2026-7KQ4XB');
    }
}
