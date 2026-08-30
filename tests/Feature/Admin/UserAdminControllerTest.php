<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UserAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_member_is_forbidden(): void
    {
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_an_admin_can_view_the_user_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $member = User::factory()->create(['is_admin' => false, 'name' => 'Jane Member']);

        $this->actingAs($admin)->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Jane Member')
            ->assertSee($admin->email);
    }

    public function test_an_admin_can_grant_admin_access_to_a_member(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $member = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($admin)->post(route('admin.users.toggle-admin', $member));

        $response->assertRedirect();
        $this->assertTrue($member->fresh()->is_admin);
    }

    public function test_an_admin_can_revoke_admin_access_from_another_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherAdmin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.users.toggle-admin', $otherAdmin));

        $this->assertFalse($otherAdmin->fresh()->is_admin);
    }

    public function test_an_admin_cannot_change_their_own_admin_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.users.toggle-admin', $admin));

        $response->assertRedirect();
        $this->assertTrue($admin->fresh()->is_admin);
    }
}
