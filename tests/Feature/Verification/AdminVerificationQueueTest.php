<?php

declare(strict_types=1);

namespace Tests\Feature\Verification;

use App\Models\CompanyVerification;
use App\Models\IdentityVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class AdminVerificationQueueTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_index_lists_both_types_and_filters(): void
    {
        $id = IdentityVerification::factory()->submitted()->create(['full_name_on_document' => 'Ada Identity']);
        $co = CompanyVerification::factory()->inReview()->create(['company_name' => 'Acme Holdings']);

        $this->actingAs($this->admin)->get(route('admin.verifications.index'))
            ->assertOk()->assertSee('Ada Identity')->assertSee('Acme Holdings');

        $this->actingAs($this->admin)->get(route('admin.verifications.index', ['type' => 'company']))
            ->assertOk()->assertSee('Acme Holdings')->assertDontSee('Ada Identity');

        $this->actingAs($this->admin)->get(route('admin.verifications.index', ['status' => 'approved']))
            ->assertOk()->assertSee('No verifications here.');
    }

    public function test_show_renders_for_both_kinds(): void
    {
        $id = IdentityVerification::factory()->submitted()->create();
        $co = CompanyVerification::factory()->submitted()->create();

        $this->actingAs($this->admin)->get(route('admin.verifications.show', ['identity', $id->id]))
            ->assertOk()->assertSee('1985-04-12')->assertSee('Approve');
        $this->actingAs($this->admin)->get(route('admin.verifications.show', ['company', $co->id]))
            ->assertOk()->assertSee($co->company_name);
    }

    public function test_non_admins_are_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))->get(route('admin.verifications.index'))->assertForbidden();
    }

    public function test_approve(): void
    {
        $row = IdentityVerification::factory()->submitted()->create();
        $this->actingAs($this->admin)->post(route('admin.verifications.approve', ['identity', $row->id]), ['notes' => 'Looks fine'])
            ->assertRedirect(route('admin.verifications.index'));
        $this->assertSame('approved', $row->fresh()->status);
    }

    public function test_reject_requires_message_for_other_and_rejects(): void
    {
        $row = CompanyVerification::factory()->submitted()->create();
        $url = route('admin.verifications.reject', ['company', $row->id]);

        $this->actingAs($this->admin)->post($url, ['reason' => 'other'])->assertSessionHasErrors('message');
        $this->actingAs($this->admin)->post($url, ['reason' => 'unreadable'])->assertRedirect(route('admin.verifications.index'));
        $this->assertSame('rejected', $row->fresh()->status);
    }

    public function test_request_info(): void
    {
        $row = IdentityVerification::factory()->inReview()->create();
        $this->actingAs($this->admin)->post(route('admin.verifications.info', ['identity', $row->id]), ['message' => 'Please upload a clearer scan.'])
            ->assertRedirect(route('admin.verifications.show', ['identity', $row->id]));
        $this->assertSame('Please upload a clearer scan.', $row->fresh()->info_request);
    }
}
