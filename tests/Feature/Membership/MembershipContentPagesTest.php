<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use Database\Seeders\MembershipTierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The content-only membership pages: Underground Plus, Business Events, and
 * the Professional Expertise / Registered Agent sections on the membership
 * index. No paid logic, quota-tracking, or billing is exercised here — that
 * is explicitly out of scope for these pages.
 */
final class MembershipContentPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MembershipTierSeeder::class);
    }

    public function test_the_plus_page_presents_plus_as_bundled_not_a_fourth_tier(): void
    {
        $this->get(route('membership.plus'))
            ->assertOk()
            ->assertSee('Underground Plus')
            ->assertSee('not a fourth')
            ->assertSee('Premium Perks')
            ->assertSee('Insurance')
            ->assertSee('Underground Travel')
            ->assertSee('Hotel Booking')
            ->assertSee('Car Rental')
            ->assertSee('Exclusive Club')
            ->assertSee('Member Rewards')
            ->assertSee('Underground Credit Cards')
            ->assertSee('Workspaces')
            ->assertSee('In preparation');
    }

    public function test_the_events_page_presents_the_tiered_benefit_structure(): void
    {
        $this->get(route('membership.events'))
            ->assertOk()
            ->assertSee('Business Events')
            ->assertSee('Corporate Affiliate')
            ->assertSee('Principal Circle')
            ->assertSee('Sovereign Partner')
            ->assertSee('15%')
            ->assertSee('30%')
            ->assertSee('Unlimited access to the covered Underground program')
            ->assertSee('subject to availability and admission rules')
            ->assertSee('Two guests per event')
            ->assertSee('five guests')
            ->assertSee('Not automatically included');
    }

    public function test_the_index_page_links_to_the_new_content_pages(): void
    {
        $this->get(route('membership.index'))
            ->assertOk()
            ->assertSee(route('membership.plus'), false)
            ->assertSee(route('membership.events'), false)
            ->assertSee('Professional Expertise')
            ->assertSee('Registered Agent')
            ->assertSee('constitute company domiciliation');
    }
}
