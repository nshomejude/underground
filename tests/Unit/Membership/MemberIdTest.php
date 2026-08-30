<?php

declare(strict_types=1);

namespace Tests\Unit\Membership;

use Domain\Membership\ValueObjects\MemberId;
use Domain\Shared\Exceptions\DomainException;
use Tests\TestCase;

final class MemberIdTest extends TestCase
{
    public function test_assign_produces_the_expected_shape(): void
    {
        $memberId = MemberId::assign(2026, 1);

        $this->assertSame('UG · 2026 · 000001', $memberId->value);
    }

    public function test_assign_zero_pads_the_sequence_to_six_digits(): void
    {
        $memberId = MemberId::assign(2026, 42);

        $this->assertSame('UG · 2026 · 000042', $memberId->value);
    }

    public function test_assign_rejects_a_sequence_out_of_range(): void
    {
        $this->expectException(DomainException::class);

        MemberId::assign(2026, 0);
    }

    public function test_from_string_round_trips_a_valid_value(): void
    {
        $memberId = MemberId::fromString('UG · 2026 · 000001');

        $this->assertSame('UG · 2026 · 000001', $memberId->value);
    }

    public function test_from_string_rejects_an_invalid_value(): void
    {
        $this->expectException(DomainException::class);

        MemberId::fromString('UGM-2026-7KQ4XB');
    }
}
