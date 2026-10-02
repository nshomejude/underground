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

final class WelcomeLetterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MembershipTierSeeder::class);
    }

    private function member(string $email, bool $approve): User
    {
        $user = User::factory()->create(['email' => $email]);

        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString($email),
            phone: null,
            country: 'Nigeria',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));

        if ($approve) {
            $application->transitionTo(MembershipApplicationStatus::UnderReview);
            ($this->app->make(ApproveMembershipApplication::class))($application);
        }

        return $user;
    }

    public function test_an_approved_member_can_read_their_personalised_welcome_letter(): void
    {
        $user = $this->member('amara@example.com', true);

        $this->actingAs($user)->get(route('account.welcome-letter'))
            ->assertOk()
            ->assertSee('Welcome to Underground')
            ->assertSee('Amara')
            ->assertSee('Principal Circle')
            ->assertSee('Founder &amp; Managing Partner', false)
            ->assertSee('images/seal/seal-gold', false)
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_a_member_without_an_approved_application_gets_no_letter(): void
    {
        $user = $this->member('pending@example.com', false);

        $this->actingAs($user)->get(route('account.welcome-letter'))->assertNotFound();
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get(route('account.welcome-letter'))->assertRedirect(route('login'));
    }

    public function test_the_seal_appears_on_the_login_page_and_site_footer(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('images/seal/seal-gold', false);
        $this->get(route('about'))->assertOk()->assertSee('images/seal/seal-gold', false);
    }
}
