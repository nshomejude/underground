<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use App\Models\MembershipCertificate;
use App\Services\CertificateService;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\Actions\ApproveMembershipApplication;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class VerifyPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MembershipTierSeeder::class);
    }

    private function approved(string $name = 'Isabelle Fontaine-Whitmore', ?string $organisation = null): MembershipCertificate
    {
        $application = ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: $name,
            organisation: $organisation,
            email: EmailAddress::fromString('private.person@example.com'),
            phone: '+33 6 12 34 56 78',
            country: 'France',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));
        $application->transitionTo(MembershipApplicationStatus::UnderReview);
        $application = ($this->app->make(ApproveMembershipApplication::class))($application);

        return $this->app->make(CertificateService::class)->ensureFor($application) ?? throw new RuntimeException('No certificate issued');
    }

    public function test_a_valid_credential_shows_public_facts_only(): void
    {
        $certificate = $this->approved();
        $this->assertSame('valid', $certificate->status());

        $response = $this->get(route('verify.show', $certificate->verify_token));

        $response->assertOk();
        $response->assertSee('Credential verified');
        $response->assertSee('Isabelle F.');
        $response->assertSee($certificate->serial);
        $response->assertSee('Principal Circle');
        $response->assertDontSee('Fontaine-Whitmore');
        $response->assertDontSee('private.person@example.com');
        $response->assertDontSee('+33 6 12 34 56 78');
        $response->assertDontSee('UG · ');
        $response->assertSee(substr(hash('sha256', $certificate->serial.'|'.$certificate->verify_token), 0, 12));
        $response->assertSee(route('contact'), false);
        $response->assertSee('noindex', false);
        $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'));
    }

    public function test_an_expired_credential_is_flagged(): void
    {
        $certificate = $this->approved();
        $certificate->update(['valid_through' => now()->subDay()]);

        $this->get(route('verify.show', $certificate->verify_token))
            ->assertOk()
            ->assertSee('Credential expired')
            ->assertSee('Expired on');
    }

    public function test_a_revoked_credential_is_flagged(): void
    {
        $certificate = $this->approved();
        $certificate->update(['revoked_at' => now()]);

        $this->get(route('verify.show', $certificate->verify_token))
            ->assertOk()
            ->assertSee('Credential revoked');
    }

    public function test_an_unknown_token_is_a_404_with_no_facts(): void
    {
        $this->approved();

        $response = $this->get(route('verify.show', 'AAAAAAAAAAAAAAAAAAAAAAAA'));

        $response->assertNotFound();
        $response->assertSee('No matching credential');
        $response->assertDontSee('Isabelle');
        $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'));
    }

    public function test_organisations_show_the_organisation_name(): void
    {
        $certificate = $this->approved('Amara Diallo', 'Diallo Holdings Ltd');

        $this->get(route('verify.show', $certificate->verify_token))
            ->assertOk()
            ->assertSee('Diallo Holdings Ltd')
            ->assertDontSee('Amara');
    }

    public function test_the_route_is_throttled(): void
    {
        $route = Route::getRoutes()->getByName('verify.show');

        $this->assertNotNull($route);
        $throttles = array_filter($route->gatherMiddleware(), static fn ($m) => str_starts_with((string) $m, 'throttle:'));
        $this->assertNotEmpty($throttles);
    }
}
