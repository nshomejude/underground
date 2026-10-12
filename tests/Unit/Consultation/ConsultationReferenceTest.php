<?php

declare(strict_types=1);

namespace Tests\Unit\Consultation;

use Domain\Consultation\ValueObjects\ConsultationReference;
use Domain\Shared\Exceptions\DomainException;
use Tests\TestCase;

final class ConsultationReferenceTest extends TestCase
{
    public function test_generate_produces_the_expected_shape(): void
    {
        $reference = ConsultationReference::generate(2026);

        $this->assertMatchesRegularExpression('/^UGC-2026-[A-HJ-NP-Z2-9]{6}$/', $reference->value);
    }

    public function test_from_string_normalises_case_and_whitespace(): void
    {
        $reference = ConsultationReference::fromString('  ugc-2026-7kq4xb  ');

        $this->assertSame('UGC-2026-7KQ4XB', $reference->value);
        $this->assertSame('UGC-2026-7KQ4XB', (string) $reference);
    }

    public function test_from_string_rejects_an_invalid_value(): void
    {
        $this->expectException(DomainException::class);

        ConsultationReference::fromString('UG-2026-7KQ4XB');
    }

    public function test_from_string_rejects_the_membership_prefix(): void
    {
        $this->expectException(DomainException::class);

        ConsultationReference::fromString('UGM-2026-7KQ4XB');
    }

    public function test_from_string_rejects_ambiguous_characters(): void
    {
        $this->expectException(DomainException::class);

        // The alphabet excludes I, O, 0, 1 to avoid ambiguity.
        ConsultationReference::fromString('UGC-2026-I0O1XX');
    }
}
