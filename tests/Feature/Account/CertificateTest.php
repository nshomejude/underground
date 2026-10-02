<?php

declare(strict_types=1);

namespace Tests\Feature\Account;

use App\Models\MembershipCertificate;
use App\Models\User;
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

final class CertificateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MembershipTierSeeder::class);
    }

    private function apply(string $email): MembershipApplication
    {
        return ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Isabelle Fontaine-Whitmore',
            organisation: null,
            email: EmailAddress::fromString($email),
            phone: null,
            country: 'France',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));
    }

    private function approved(string $email): MembershipApplication
    {
        $application = $this->apply($email);
        $application->transitionTo(MembershipApplicationStatus::UnderReview);

        return ($this->app->make(ApproveMembershipApplication::class))($application);
    }

    public function test_an_approved_member_gets_the_certificate(): void
    {
        $user = User::factory()->create(['email' => 'cert@example.com']);
        $this->approved('cert@example.com');

        $response = $this->actingAs($user)->get(route('account.certificate'));

        $response->assertOk();
        $response->assertSee('Certificate of Membership');
        $response->assertSee('Scan to verify');
        $response->assertSee('Isabelle Fontaine-Whitmore');

        $certificate = MembershipCertificate::query()->firstOrFail();
        $response->assertSee($certificate->serial);
        $response->assertSee($certificate->verify_token);
        $response->assertSee('QR code linking to', false);
        $response->assertSee('<svg', false);
        $this->assertStringContainsString('private', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'));
    }

    public function test_a_pending_member_gets_404(): void
    {
        $user = User::factory()->create(['email' => 'pending@example.com']);
        $this->apply('pending@example.com');

        $this->actingAs($user)->get(route('account.certificate'))->assertNotFound();
        $this->assertSame(0, MembershipCertificate::query()->count());
    }

    public function test_a_member_without_an_application_gets_404(): void
    {
        $this->actingAs(User::factory()->create())->get(route('account.certificate'))->assertNotFound();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('account.certificate'))->assertRedirect(route('login'));
    }

    public function test_another_member_cannot_see_it(): void
    {
        $this->approved('owner@example.com');
        $other = User::factory()->create(['email' => 'other@example.com']);

        $this->actingAs($other)->get(route('account.certificate'))->assertNotFound();
    }

    public function test_a_revoked_certificate_gets_404(): void
    {
        $user = User::factory()->create(['email' => 'revoked@example.com']);
        $this->approved('revoked@example.com');

        $this->actingAs($user)->get(route('account.certificate'))->assertOk();

        MembershipCertificate::query()->update(['revoked_at' => now()]);

        $this->actingAs($user)->get(route('account.certificate'))->assertNotFound();
    }
}
