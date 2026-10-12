<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\TeamMemberRecord;
use Tests\TestCase;

final class TeamMemberAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_create_a_team_member_with_a_role_category_and_languages(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.team.store'), [
            'slug' => 'new-member',
            'name' => 'New Member',
            'title' => 'Board Member',
            'background' => 'A new board member.',
            'icon' => 'users',
            'role_category' => 'governance',
            'employment_status' => 'independent',
            'languages' => 'English, French',
            'position' => 7,
        ]);

        $response->assertRedirect(route('admin.team.index'));
        $this->assertDatabaseHas('team_members', [
            'slug' => 'new-member',
            'role_category' => 'governance',
            'employment_status' => 'independent',
        ]);

        $member = TeamMemberRecord::query()->where('slug', 'new-member')->first();
        $this->assertSame(['English', 'French'], $member->languages);
    }

    public function test_creating_a_team_member_rejects_a_role_category_outside_the_enum(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.team.store'), [
            'slug' => 'bad-category',
            'name' => 'Bad Category',
            'title' => 'Partner',
            'background' => 'Background.',
            'icon' => 'target',
            'role_category' => 'not-a-real-category',
            'employment_status' => 'employee',
            'position' => 1,
        ]);

        $response->assertSessionHasErrors(['role_category']);
        $this->assertDatabaseMissing('team_members', ['slug' => 'bad-category']);
    }
}
