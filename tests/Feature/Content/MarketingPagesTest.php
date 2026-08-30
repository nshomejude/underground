<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\EventRecord;
use Infrastructure\Persistence\Eloquent\Models\PartnerCategoryRecord;
use Infrastructure\Persistence\Eloquent\Models\PortfolioEngagementRecord;
use Infrastructure\Persistence\Eloquent\Models\ProjectRecord;
use Infrastructure\Persistence\Eloquent\Models\TeamMemberRecord;
use Tests\TestCase;

final class MarketingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Power Beneath the Surface.')
            ->assertSee('Discretion First');
    }

    public function test_team_page_renders(): void
    {
        TeamMemberRecord::factory()->create([
            'name' => 'Tony Smith',
            'title' => 'Founder & Managing Partner',
            'has_portrait' => true,
            'icon' => null,
            'position' => 1,
        ]);

        $this->get(route('team'))
            ->assertOk()
            ->assertSee('The Partners')
            ->assertSee('Founder &amp; Managing Partner', false);
    }

    public function test_contact_page_renders_and_links_to_the_confidential_inquiry_form(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('office@underground-network.example')
            ->assertSee(route('inquiries.create'), false);
    }

    public function test_partners_page_renders(): void
    {
        PartnerCategoryRecord::factory()->create(['title' => 'Advisory Firms', 'position' => 1]);
        PartnerCategoryRecord::factory()->create(['title' => 'Multilateral Organizations', 'position' => 2]);

        $this->get(route('partners'))
            ->assertOk()
            ->assertSee('Advisory Firms')
            ->assertSee('Multilateral Organizations');
    }

    public function test_collaboration_page_renders(): void
    {
        $this->get(route('collaboration'))
            ->assertOk()
            ->assertSee('Embedded Teams')
            ->assertSee('Secure Channels');
    }

    public function test_portfolio_page_renders_sector_tagged_engagements(): void
    {
        PortfolioEngagementRecord::factory()->create(['sector' => 'Government & Public Sector', 'position' => 1]);
        PortfolioEngagementRecord::factory()->create(['sector' => 'Defense & Security', 'position' => 2]);

        $this->get(route('portfolio'))
            ->assertOk()
            ->assertSee('Selected Engagements')
            ->assertSee('Government &amp; Public Sector', false)
            ->assertSee('Defense &amp; Security', false);
    }

    public function test_projects_page_renders(): void
    {
        ProjectRecord::factory()->create(['title' => 'Ministerial Transition Advisory Program', 'position' => 1]);

        $this->get(route('projects'))
            ->assertOk()
            ->assertSee('Ministerial Transition Advisory Program')
            ->assertSee('Ongoing');
    }

    public function test_events_page_renders_past_and_upcoming_events(): void
    {
        EventRecord::factory()->create([
            'name' => 'Underground Winter Roundtable',
            'date' => now()->subYear()->toDateString(),
            'position' => 1,
        ]);
        EventRecord::factory()->create([
            'date' => now()->addYear()->toDateString(),
            'position' => 2,
        ]);

        $this->get(route('events'))
            ->assertOk()
            ->assertSee('Underground Winter Roundtable')
            ->assertSee('Past')
            ->assertSee('Upcoming');
    }
}
