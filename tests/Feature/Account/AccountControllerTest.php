<?php

declare(strict_types=1);

namespace Tests\Feature\Account;

use App\Models\User;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MembershipTierSeeder::class);
    }

    public function test_an_approved_application_renders_the_real_membership_card(): void
    {
        $user = User::factory()->create(['email' => 'approved@example.com']);

        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Isabelle Fontaine-Whitmore',
            organisation: null,
            email: EmailAddress::fromString('APPROVED@example.com'),
            phone: null,
            country: 'France',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));

        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        $application = ($this->app->make(ApproveMembershipApplication::class))($application);

        $response = $this->actingAs($user)->get(route('account.show'));

        $response->assertOk();
        $response->assertSee('Your Membership Card');
        $response->assertSee($application->memberId()->value);
        $response->assertSee('Isabelle Fontaine-Whitmore');
        $this->assertMatchesRegularExpression('/^UG · \d{4} · \d{6}$/', $application->memberId()->value);
    }

    public function test_email_matching_is_case_insensitive(): void
    {
        $user = User::factory()->create(['email' => 'Case.Test@example.com']);

        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Case Test',
            organisation: null,
            email: EmailAddress::fromString('case.test@example.com'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));

        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        ($this->app->make(ApproveMembershipApplication::class))($application);

        $this->actingAs($user)->get(route('account.show'))
            ->assertOk()
            ->assertSee('Your Membership Card');
    }

    public function test_a_pending_application_shows_the_under_review_state(): void
    {
        $user = User::factory()->create(['email' => 'pending@example.com']);

        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('corporate-affiliate'),
            name: 'Marcus Reyes',
            organisation: 'Castellane Atlantic Holdings',
            email: EmailAddress::fromString('pending@example.com'),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a corporate affiliate relationship with Underground.',
        ));

        $response = $this->actingAs($user)->get(route('account.show'));

        $response->assertOk();
        $response->assertSee('Application Under Review');
        $response->assertSee($application->reference->value);
        $response->assertDontSee('Your Membership Card');
    }

    public function test_a_member_with_no_application_sees_an_invitation_to_apply(): void
    {
        $user = User::factory()->create(['email' => 'nobody@example.com']);

        $response = $this->actingAs($user)->get(route('account.show'));

        $response->assertOk();
        $response->assertSee(route('membership.index'));
        $response->assertDontSee('Your Membership Card');
        $response->assertDontSee('Application Under Review');
    }

    public function test_account_redirects_to_login_when_unauthenticated(): void
    {
        $this->get(route('account.show'))->assertRedirect(route('login'));
    }
}
