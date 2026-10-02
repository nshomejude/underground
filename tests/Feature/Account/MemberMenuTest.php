<?php

declare(strict_types=1);

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MemberMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_member_page_carries_the_full_menu(): void
    {
        $user = User::factory()->create();

        foreach (['account.show', 'account.applications', 'account.documents', 'account.security', 'account.settings'] as $name) {
            $response = $this->actingAs($user)->get(route($name))->assertOk();

            foreach (['Overview', 'Membership Card', 'Certificate', 'Applications', 'Inquiries', 'Documents', 'Security', 'Settings', 'Sign out'] as $label) {
                $response->assertSee($label);
            }
        }
    }

    public function test_the_new_member_pages_require_login(): void
    {
        foreach (['account.applications', 'account.documents', 'account.security'] as $name) {
            $this->get(route($name))->assertRedirect(route('login'));
        }
    }
}
