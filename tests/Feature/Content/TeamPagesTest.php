<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\GalleryPhotoRecord;
use Infrastructure\Persistence\Eloquent\Models\PortfolioEngagementRecord;
use Infrastructure\Persistence\Eloquent\Models\TeamMemberRecord;
use Tests\Concerns\RegistersTeamRoutes;
use Tests\TestCase;

final class TeamPagesTest extends TestCase
{
    use RefreshDatabase;
    use RegistersTeamRoutes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerTeamRoutes();
    }

    public function test_senior_leadership_splits_members_into_their_three_tabs(): void
    {
        TeamMemberRecord::factory()->create([
            'slug' => 'the-founder',
            'name' => 'The Founder',
            'title' => 'Founder & Managing Partner',
            'role_category' => 'executive',
            'position' => 1,
        ]);
        TeamMemberRecord::factory()->create([
            'slug' => 'a-board-member',
            'name' => 'A Board Member',
            'title' => 'Board Member',
            'role_category' => 'governance',
            'position' => 2,
        ]);
        TeamMemberRecord::factory()->create([
            'slug' => 'an-advisor',
            'name' => 'An Advisor',
            'title' => 'Advisory Board Member',
            'role_category' => 'governance',
            'position' => 3,
        ]);
        TeamMemberRecord::factory()->create([
            'slug' => 'a-partner',
            'name' => 'A Partner',
            'title' => 'Partner, Something',
            'role_category' => 'expertise',
            'position' => 4,
        ]);

        $response = $this->get(route('team.senior-leadership'));

        $response->assertOk();
        $response->assertSee('The Founder');
        $response->assertSee('A Board Member');
        $response->assertSee('An Advisor');
        // Expertise-tier principals belong on the main roster, not here.
        $response->assertDontSee('A Partner');
    }

    public function test_senior_leadership_shows_an_empty_state_for_a_tab_with_no_members(): void
    {
        TeamMemberRecord::factory()->create([
            'slug' => 'the-founder',
            'title' => 'Founder & Managing Partner',
            'role_category' => 'executive',
        ]);

        $response = $this->get(route('team.senior-leadership'));

        $response->assertOk();
        $response->assertSee('No Board Members are publicly listed at this time.');
        $response->assertSee('No Advisory Board members are publicly listed at this time.');
    }

    public function test_gallery_shows_the_empty_state_when_there_are_no_photos(): void
    {
        $response = $this->get(route('team.gallery'));

        $response->assertOk();
        $response->assertSee('No photos are published here yet.');
    }

    public function test_gallery_shows_a_photo_with_its_rights_note_and_a_pending_tile_without_an_image(): void
    {
        GalleryPhotoRecord::factory()->create([
            'caption' => 'Partners at the Washington office',
            'photo_rights_note' => 'Consent given by all pictured.',
            'image_url' => null,
        ]);

        $response = $this->get(route('team.gallery'));

        $response->assertOk();
        $response->assertSee('Partners at the Washington office');
        $response->assertSee('Consent given by all pictured.');
        $response->assertSee('Photo Pending');
    }

    public function test_case_studies_shows_the_full_case_study_field_set(): void
    {
        PortfolioEngagementRecord::factory()->create([
            'title' => 'A Relocated Case Study',
            'real_role' => 'Retained advisor to the ministry.',
            'deliverables' => ['Strategy memo', 'Stakeholder plan'],
            'period' => '7 months',
            'publication_agreement_note' => 'No publication agreement on file.',
        ]);

        $response = $this->get(route('team.case-studies'));

        $response->assertOk();
        $response->assertSee('A Relocated Case Study');
        $response->assertSee('Retained advisor to the ministry.');
        $response->assertSee('Strategy memo');
        $response->assertSee('7 months');
        $response->assertSee('No publication agreement on file.');
    }
}
