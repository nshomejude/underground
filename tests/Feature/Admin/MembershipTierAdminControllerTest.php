<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;
use Tests\TestCase;

final class MembershipTierAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.membership-tiers.index'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_member_is_forbidden(): void
    {
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get(route('admin.membership-tiers.index'))->assertForbidden();
    }

    public function test_an_admin_can_view_the_tier_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        MembershipTierRecord::factory()->create(['name' => 'Sovereign Partner']);

        $this->actingAs($admin)->get(route('admin.membership-tiers.index'))
            ->assertOk()
            ->assertSee('Sovereign Partner');
    }

    public function test_an_admin_can_create_a_tier(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.membership-tiers.store'), [
            'name' => 'Global Circle',
            'slug' => 'global-circle',
            'audience' => 'Cross-border allocators of consequence.',
            'icon' => 'globe',
            'position' => 4,
        ]);

        $response->assertRedirect(route('admin.membership-tiers.index'));
        $this->assertDatabaseHas('membership_tiers', ['slug' => 'global-circle', 'name' => 'Global Circle']);
    }

    public function test_an_admin_can_update_a_tier(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $tier = MembershipTierRecord::factory()->create(['slug' => 'principal-circle']);

        $response = $this->actingAs($admin)->put(route('admin.membership-tiers.update', 'principal-circle'), [
            'name' => 'Updated Name',
            'slug' => 'principal-circle',
            'audience' => $tier->audience,
            'icon' => $tier->icon,
            'position' => $tier->position,
        ]);

        $response->assertRedirect(route('admin.membership-tiers.index'));
        $this->assertDatabaseHas('membership_tiers', ['slug' => 'principal-circle', 'name' => 'Updated Name']);
    }

    public function test_an_admin_can_delete_a_tier(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        MembershipTierRecord::factory()->create(['slug' => 'corporate-affiliate']);

        $response = $this->actingAs($admin)->delete(route('admin.membership-tiers.destroy', 'corporate-affiliate'));

        $response->assertRedirect(route('admin.membership-tiers.index'));
        $this->assertDatabaseMissing('membership_tiers', ['slug' => 'corporate-affiliate']);
    }
}
