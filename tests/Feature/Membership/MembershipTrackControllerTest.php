<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MembershipTrackControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MembershipTierSeeder::class);
    }

    private function submitApplication(): MembershipApplication
    {
        return ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('sovereign-partner'),
            name: 'Amara Okafor',
            organisation: 'Ministry of Trade',
            email: EmailAddress::fromString('amara.okafor@example.com'),
            phone: '+234 800 000 0000',
            country: 'Nigeria',
            statement: 'We are seeking a discreet strategic partner to advise on regional infrastructure financing.',
        ));
    }

    public function test_the_track_page_renders_with_no_reference_supplied(): void
    {
        $this->get(route('membership.track'))
            ->assertOk()
            ->assertSee('Track Your Application');
    }

    public function test_a_valid_reference_shows_the_correct_status_and_tier(): void
    {
        $application = $this->submitApplication();

        $response = $this->get(route('membership.track', ['reference' => $application->reference->value]));

        $response->assertOk();
        $response->assertSee($application->reference->value);
        $response->assertSee('Submitted');
        $response->assertSee('Sovereign Partner');
        $response->assertSee($application->submittedAt->format('j M Y'));
    }

    public function test_an_approved_application_points_to_register_or_login_without_showing_a_card(): void
    {
        $application = $this->submitApplication();

        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        ($this->app->make(ApproveMembershipApplication::class))($application);

        $response = $this->get(route('membership.track', ['reference' => $application->reference->value]));

        $response->assertOk();
        $response->assertSee('Approved');
        $response->assertSee('register or log in to view your membership card', false);
        $response->assertSee(route('register'), false);
        $response->assertSee(route('login'), false);
    }

    public function test_the_public_view_never_renders_private_details(): void
    {
        $application = $this->submitApplication();

        $response = $this->get(route('membership.track', ['reference' => $application->reference->value]));

        $response->assertOk();
        $response->assertDontSee($application->name);
        $response->assertDontSee($application->organisation);
        $response->assertDontSee($application->email->value);
        $response->assertDontSee($application->phone);
        $response->assertDontSee($application->statement);
    }

    public function test_an_unknown_but_well_formed_reference_shows_a_friendly_message(): void
    {
        $response = $this->get(route('membership.track', ['reference' => 'UGM-2026-ZZZZZZ']));

        $response->assertOk();
        $response->assertSee('couldn&rsquo;t find an application', false);
    }

    public function test_a_malformed_reference_shows_a_friendly_message_not_a_500(): void
    {
        $response = $this->get(route('membership.track', ['reference' => 'not-a-real-reference']));

        $response->assertOk();
        $response->assertSee('couldn&rsquo;t find an application', false);
    }
}
