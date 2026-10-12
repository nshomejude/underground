<?php

declare(strict_types=1);

namespace Tests\Unit\Content;

use Domain\Content\ValueObjects\RelationshipType;
use Tests\TestCase;

final class RelationshipTypeTest extends TestCase
{
    public function test_label_returns_a_human_readable_string_for_every_case(): void
    {
        $this->assertSame('Joint Venture', RelationshipType::JointVenture->label());
        $this->assertSame('Partnership', RelationshipType::Partnership->label());
        $this->assertSame('Cooperation Agreement', RelationshipType::CooperationAgreement->label());
        $this->assertSame('Consultant', RelationshipType::Consultant->label());
        $this->assertSame('Independent', RelationshipType::Independent->label());
    }

    public function test_only_independent_describes_underground_itself(): void
    {
        foreach (RelationshipType::cases() as $type) {
            $description = $type->description();

            $this->assertNotSame('', $description);

            if ($type !== RelationshipType::Independent) {
                $this->assertStringContainsString('not', $description);
            }
        }

        $this->assertStringContainsString('Directly operated by Underground', RelationshipType::Independent->description());
    }
}
