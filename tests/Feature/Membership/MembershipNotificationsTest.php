<?php

declare(strict_types=1);

namespace Tests\Feature\Membership;

use App\Models\MembershipCertificate;
use App\Models\User;
use App\Notifications\MembershipStatusNotification;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class MembershipNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MembershipTierSeeder::class);
    }

    private function applyFor(string $email): MembershipApplication
    {
        return ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString($email),
            phone: null,
            country: 'Nigeria',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));
    }

    public function test_submitting_an_application_emails_the_applicant(): void
    {
        Notification::fake();

        $this->post(route('membership.store', ['tier' => 'principal-circle']), [
            'applicant_name' => 'Amara Diallo',
            'email' => 'amara@example.com',
            'country' => 'Nigeria',
            'statement' => 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ])->assertRedirect();

        Notification::assertSentOnDemand(MembershipStatusNotification::class, function ($notification, $channels, $notifiable) {
            return $notification->stage === 'received' && $notifiable->routes['mail'] === 'amara@example.com';
        });
    }

    public function test_approval_issues_a_certificate_and_notifies_a_member_with_an_account(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $member = User::factory()->create(['email' => 'amara@example.com']);
        $application = $this->applyFor('amara@example.com');

        $this->actingAs($admin)->post(route('admin.applications.approve', ['reference' => $application->reference->value]))
            ->assertRedirect();

        $this->assertDatabaseCount('membership_certificates', 1);
        $this->assertNotNull(MembershipCertificate::query()->first()->serial);

        Notification::assertSentTo($member, MembershipStatusNotification::class, fn ($n) => $n->stage === 'approved');
    }

    public function test_decline_notifies_the_applicant(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $application = $this->applyFor('nobody@example.com');

        $this->actingAs($admin)->post(route('admin.applications.decline', ['reference' => $application->reference->value]))
            ->assertRedirect();

        Notification::assertSentOnDemand(MembershipStatusNotification::class, fn ($n) => $n->stage === 'declined');
    }

    public function test_the_approved_email_renders_with_the_branded_template(): void
    {
        $n = new MembershipStatusNotification('approved', [
            'reference' => 'UGM-2026-ABC234',
            'tier' => 'Principal Circle',
            'name' => 'Amara Diallo',
            'memberId' => 'UG · 2026 · 000001',
            'serial' => 'UGC-2026-000001',
        ]);

        $mail = $n->toMail(new User);
        $html = (string) view($mail->view[0], $mail->viewData)->render();

        $this->assertStringContainsString('membership is approved', $html);
        $this->assertStringContainsString('UGC-2026-000001', $html);
    }
}
