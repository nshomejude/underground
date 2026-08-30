<?php

declare(strict_types=1);

namespace Tests\Unit\Shared;

use Domain\Shared\Exceptions\DomainException;
use Domain\Shared\ValueObjects\Slug;
use Tests\TestCase;

final class SlugTest extends TestCase
{
    public function test_from_string_normalises_case_and_whitespace(): void
    {
        $slug = Slug::fromString('  Strategic-Intelligence  ');

        $this->assertSame('strategic-intelligence', $slug->value);
        $this->assertSame('strategic-intelligence', (string) $slug);
    }

    public function test_from_string_rejects_a_blank_value(): void
    {
        $this->expectException(DomainException::class);

        Slug::fromString('   ');
    }

    public function test_from_string_rejects_a_value_with_invalid_characters(): void
    {
        $this->expectException(DomainException::class);

        Slug::fromString('Not A Valid Slug!');
    }

    public function test_equals_compares_by_value(): void
    {
        $a = Slug::fromString('strategic-intelligence');
        $b = Slug::fromString('strategic-intelligence');
        $c = Slug::fromString('government-affairs');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }
}
