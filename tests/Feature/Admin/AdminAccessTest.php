<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.applications.index'))->assertRedirect(route('login'));
        $this->get(route('admin.inquiries.index'))->assertRedirect(route('login'));
    }

    public function test_a_logged_in_non_admin_gets_a_403(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.applications.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.inquiries.index'))->assertForbidden();
    }

    public function test_an_admin_can_reach_the_review_queues(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.applications.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.inquiries.index'))->assertOk();
    }
}
