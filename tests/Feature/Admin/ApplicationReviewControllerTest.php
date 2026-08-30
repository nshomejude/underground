<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Application\Membership\Queries\ListMembershipApplications;
use Database\Seeders\MembershipTierSeeder;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ApplicationReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MembershipTierSeeder::class);

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    private function submitApplication(string $email = 'applicant@example.com'): MembershipApplication
    {
        return ($this->app->make(ApplyForMembership::class))(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Amara Diallo',
            organisation: null,
            email: EmailAddress::fromString($email),
            phone: null,
            country: null,
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));
    }

    public function test_the_index_lists_applications_with_status_badges(): void
    {
        $application = $this->submitApplication();

        $response = $this->actingAs($this->admin)->get(route('admin.applications.index'));

        $response->assertOk();
        $response->assertSee($application->reference->value);
        $response->assertSee($application->name);
        $response->assertSee('Submitted');
    }

    public function test_an_admin_can_approve_a_pending_application(): void
    {
        $application = $this->submitApplication();

        $response = $this->actingAs($this->admin)->post(
            route('admin.applications.approve', $application->reference->value),
        );

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $updated = ($this->app->make(ListMembershipApplications::class))();
        $updated = collect($updated)->first(fn (MembershipApplication $a) => $a->reference->value === $application->reference->value);

        $this->assertSame(MembershipApplicationStatus::Approved, $updated->status());
        $this->assertNotNull($updated->memberId());
    }

    public function test_an_admin_can_decline_a_pending_application(): void
    {
        $application = $this->submitApplication('declined-applicant@example.com');

        $response = $this->actingAs($this->admin)->post(
            route('admin.applications.decline', $application->reference->value),
        );

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $updated = ($this->app->make(ListMembershipApplications::class))();
        $updated = collect($updated)->first(fn (MembershipApplication $a) => $a->reference->value === $application->reference->value);

        $this->assertSame(MembershipApplicationStatus::Declined, $updated->status());
        $this->assertNull($updated->memberId());
    }

    public function test_approving_an_already_declined_application_fails_gracefully(): void
    {
        $application = $this->submitApplication('already-declined@example.com');

        $this->actingAs($this->admin)->post(route('admin.applications.decline', $application->reference->value));

        $response = $this->actingAs($this->admin)->post(
            route('admin.applications.approve', $application->reference->value),
        );

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_a_non_admin_cannot_approve_an_application(): void
    {
        $application = $this->submitApplication('blocked-applicant@example.com');
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->post(
            route('admin.applications.approve', $application->reference->value),
        )->assertForbidden();
    }
}
