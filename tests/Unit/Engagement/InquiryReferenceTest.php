<?php

declare(strict_types=1);

namespace Tests\Unit\Engagement;

use Domain\Engagement\ValueObjects\InquiryReference;
use Domain\Shared\Exceptions\DomainException;
use Tests\TestCase;

final class InquiryReferenceTest extends TestCase
{
    public function test_generate_produces_the_expected_shape(): void
    {
        $reference = InquiryReference::generate(2026);

        $this->assertMatchesRegularExpression('/^UG-2026-[A-HJ-NP-Z2-9]{6}$/', $reference->value);
    }

    public function test_from_string_normalises_case_and_whitespace(): void
    {
        $reference = InquiryReference::fromString('  ug-2026-7kq4xb  ');

        $this->assertSame('UG-2026-7KQ4XB', $reference->value);
        $this->assertSame('UG-2026-7KQ4XB', (string) $reference);
    }

    public function test_from_string_rejects_an_invalid_value(): void
    {
        $this->expectException(DomainException::class);

        InquiryReference::fromString('UGM-2026-7KQ4XB');
    }

    public function test_from_string_rejects_ambiguous_characters(): void
    {
        $this->expectException(DomainException::class);

        // The alphabet excludes I, O, 0, 1 to avoid ambiguity.
        InquiryReference::fromString('UG-2026-I0O1XX');
    }
}
