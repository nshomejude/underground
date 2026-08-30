<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\EngagementModelRecord;
use Tests\TestCase;

final class EngagementModelAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.engagement-models.index'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_member_is_forbidden(): void
    {
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get(route('admin.engagement-models.index'))->assertForbidden();
    }

    public function test_an_admin_can_view_the_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        EngagementModelRecord::factory()->create(['name' => 'Strategic Advisory Retainers']);

        $this->actingAs($admin)->get(route('admin.engagement-models.index'))
            ->assertOk()
            ->assertSee('Strategic Advisory Retainers');
    }

    public function test_an_admin_can_create_an_engagement_model(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.engagement-models.store'), [
            'name' => 'Project-Based Engagements',
            'slug' => 'project-based-engagements',
            'summary' => 'Defined-scope, defined-term work.',
            'icon' => 'target',
            'position' => 2,
        ]);

        $response->assertRedirect(route('admin.engagement-models.index'));
        $this->assertDatabaseHas('engagement_models', ['slug' => 'project-based-engagements']);
    }

    public function test_creating_an_engagement_model_validates_required_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.engagement-models.store'), [
            'name' => '',
            'slug' => '',
            'summary' => '',
            'icon' => 'target',
            'position' => 2,
        ]);

        $response->assertSessionHasErrors(['name', 'slug', 'summary']);
        $this->assertDatabaseCount('engagement_models', 0);
    }

    public function test_an_admin_can_view_the_edit_form(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        EngagementModelRecord::factory()->create(['slug' => 'project-based-engagements', 'name' => 'Project-Based Engagements']);

        $this->actingAs($admin)->get(route('admin.engagement-models.edit', 'project-based-engagements'))
            ->assertOk()
            ->assertSee('Project-Based Engagements');
    }

    public function test_an_admin_can_update_an_engagement_model(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        EngagementModelRecord::factory()->create(['slug' => 'project-based-engagements', 'name' => 'Old Name']);

        $response = $this->actingAs($admin)->put(route('admin.engagement-models.update', 'project-based-engagements'), [
            'name' => 'Project-Based Engagements',
            'slug' => 'project-based-engagements',
            'summary' => 'Updated summary.',
            'icon' => 'target',
            'position' => 2,
        ]);

        $response->assertRedirect(route('admin.engagement-models.index'));
        $this->assertDatabaseHas('engagement_models', ['slug' => 'project-based-engagements', 'name' => 'Project-Based Engagements']);
    }

    public function test_an_admin_can_delete_an_engagement_model(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        EngagementModelRecord::factory()->create(['slug' => 'project-based-engagements']);

        $response = $this->actingAs($admin)->delete(route('admin.engagement-models.destroy', 'project-based-engagements'));

        $response->assertRedirect(route('admin.engagement-models.index'));
        $this->assertDatabaseMissing('engagement_models', ['slug' => 'project-based-engagements']);
    }
}
