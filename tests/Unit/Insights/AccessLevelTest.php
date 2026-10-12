<?php

declare(strict_types=1);

namespace Tests\Unit\Insights;

use Domain\Insights\ValueObjects\AccessLevel;
use Tests\TestCase;

final class AccessLevelTest extends TestCase
{
    public function test_public_access_does_not_require_membership(): void
    {
        $this->assertFalse(AccessLevel::PublicAccess->requiresMembership());
        $this->assertSame('Public', AccessLevel::PublicAccess->label());
        $this->assertSame('public', AccessLevel::PublicAccess->value);
    }

    public function test_members_only_requires_membership(): void
    {
        $this->assertTrue(AccessLevel::MembersOnly->requiresMembership());
        $this->assertSame('Member Access', AccessLevel::MembersOnly->label());
        $this->assertSame('members', AccessLevel::MembersOnly->value);
    }

    public function test_from_value_round_trips(): void
    {
        $this->assertSame(AccessLevel::PublicAccess, AccessLevel::from('public'));
        $this->assertSame(AccessLevel::MembersOnly, AccessLevel::from('members'));
    }
}
