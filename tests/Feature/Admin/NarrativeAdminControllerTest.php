<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\NarrativeRecord;
use Tests\TestCase;

final class NarrativeAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.narrative.edit'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_member_is_forbidden(): void
    {
        NarrativeRecord::factory()->create();
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get(route('admin.narrative.edit'))->assertForbidden();
    }

    public function test_an_admin_can_view_the_narrative_edit_form(): void
    {
        NarrativeRecord::factory()->create(['tagline' => 'Strategic Influence. Real Outcomes.']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.narrative.edit'))
            ->assertOk()
            ->assertSee('Strategic Influence. Real Outcomes.');
    }

    /** @return array<string, mixed> */
    private function validPayload(): array
    {
        return [
            'company' => 'Underground',
            'tagline' => 'Revised Tagline.',
            'eyebrow' => 'The Firm',
            'headline_text' => "Power Beneath\nthe Surface.",
            'accent_line' => 'An accent line.',
            'intro' => 'An intro paragraph.',
            'primary_cta_label' => 'Start a confidential conversation',
            'primary_cta_href' => '#inquiry',
            'secondary_cta_label' => 'Explore capabilities',
            'secondary_cta_href' => '#capabilities',
            'creed_title' => 'Our Creed',
            'creed_body' => 'A creed body.',
            'capabilities_eyebrow' => 'What We Do',
            'capabilities_heading' => 'Capabilities Heading',
            'sectors_heading' => 'Sectors Heading',
            'reach_heading' => 'Reach Heading',
            'reach_body' => 'A reach body.',
            'reach_cta_label' => 'View our reach',
            'reach_cta_href' => '#reach',
            'engagement_heading' => 'Engagement Heading',
            'closing_heading' => 'Closing Heading',
            'closing_support' => 'A closing support paragraph.',
            'closing_cta_label' => 'Start a confidential conversation',
            'closing_cta_href' => '#inquiry',
            'navigation' => [
                ['label' => 'Capabilities', 'href' => '#capabilities'],
                ['label' => '', 'href' => ''],
            ],
            'copyright' => '© 2026 Underground. All rights reserved.',
        ];
    }

    public function test_an_admin_can_update_the_narrative(): void
    {
        NarrativeRecord::factory()->create(['tagline' => 'Old Tagline']);
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->put(route('admin.narrative.update'), $this->validPayload());

        $response->assertRedirect(route('admin.narrative.edit'));
        $this->assertDatabaseHas('narratives', ['tagline' => 'Revised Tagline.']);
        $this->assertSame(1, NarrativeRecord::count());
    }

    public function test_updating_the_narrative_assembles_the_headline_and_navigation_lists(): void
    {
        NarrativeRecord::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put(route('admin.narrative.update'), $this->validPayload());

        $record = NarrativeRecord::query()->latest('id')->first();

        $this->assertSame(['Power Beneath', 'the Surface.'], $record->headline);
        $this->assertSame([['label' => 'Capabilities', 'href' => '#capabilities']], $record->navigation);
    }

    public function test_updating_the_narrative_validates_required_fields(): void
    {
        NarrativeRecord::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $payload = $this->validPayload();
        $payload['company'] = '';
        $payload['tagline'] = '';

        $response = $this->actingAs($admin)->put(route('admin.narrative.update'), $payload);

        $response->assertSessionHasErrors(['company', 'tagline']);
    }
}
