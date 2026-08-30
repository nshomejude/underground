<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Tests\TestCase;

final class MarketingPagesTest extends TestCase
{
    public function test_about_page_renders(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Power Beneath the Surface.')
            ->assertSee('Discretion First');
    }

    public function test_team_page_renders(): void
    {
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
        $this->get(route('portfolio'))
            ->assertOk()
            ->assertSee('Selected Engagements')
            ->assertSee('Government &amp; Public Sector', false)
            ->assertSee('Defense &amp; Security', false);
    }

    public function test_projects_page_renders(): void
    {
        $this->get(route('projects'))
            ->assertOk()
            ->assertSee('Ministerial Transition Advisory Program')
            ->assertSee('Ongoing');
    }

    public function test_events_page_renders_past_and_upcoming_events(): void
    {
        $this->get(route('events'))
            ->assertOk()
            ->assertSee('Underground Winter Roundtable')
            ->assertSee('Past')
            ->assertSee('Upcoming');
    }
}
