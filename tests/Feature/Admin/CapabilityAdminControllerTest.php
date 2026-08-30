<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;
use Tests\TestCase;

final class CapabilityAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_list_capabilities(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        CapabilityRecord::factory()->create(['title' => 'Strategic Intelligence & Analysis']);

        $this->actingAs($admin)->get(route('admin.capabilities.index'))
            ->assertOk()
            ->assertSee('Strategic Intelligence & Analysis');
    }

    public function test_an_admin_can_create_a_capability(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.capabilities.store'), [
            'slug' => 'new-capability',
            'title' => 'New Capability',
            'summary' => 'A summary of the new capability.',
            'icon' => 'landmark',
            'position' => 5,
            'is_featured' => '1',
        ]);

        $response->assertRedirect(route('admin.capabilities.index'));
        $this->assertDatabaseHas('capabilities', [
            'slug' => 'new-capability',
            'title' => 'New Capability',
            'position' => 5,
            'is_featured' => true,
        ]);
    }

    public function test_creating_a_capability_validates_required_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.capabilities.store'), [
            'slug' => 'incomplete',
        ]);

        $response->assertSessionHasErrors(['title', 'summary', 'icon', 'position']);
        $this->assertDatabaseMissing('capabilities', ['slug' => 'incomplete']);
    }

    public function test_creating_a_capability_rejects_an_icon_outside_the_whitelist(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.capabilities.store'), [
            'slug' => 'bad-icon',
            'title' => 'Bad Icon',
            'summary' => 'A summary.',
            'icon' => 'not-a-real-icon',
            'position' => 1,
        ]);

        $response->assertSessionHasErrors(['icon']);
    }

    public function test_an_admin_can_edit_a_capability(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $capability = CapabilityRecord::factory()->create(['slug' => 'existing-capability', 'title' => 'Original Title']);

        $this->actingAs($admin)->get(route('admin.capabilities.edit', $capability->slug))
            ->assertOk()
            ->assertSee('Original Title');

        $response = $this->actingAs($admin)->put(route('admin.capabilities.update', $capability->slug), [
            'title' => 'Updated Title',
            'summary' => $capability->summary,
            'icon' => $capability->icon,
            'position' => $capability->position,
        ]);

        $response->assertRedirect(route('admin.capabilities.index'));
        $this->assertDatabaseHas('capabilities', ['slug' => 'existing-capability', 'title' => 'Updated Title']);
    }

    public function test_an_admin_can_delete_a_capability_without_affecting_insights(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $capability = CapabilityRecord::factory()->create();
        InsightRecord::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.capabilities.destroy', $capability->slug));

        $response->assertRedirect(route('admin.capabilities.index'));
        $this->assertDatabaseMissing('capabilities', ['slug' => $capability->slug]);
        $this->assertDatabaseCount('insights', 1);
    }

    public function test_a_non_admin_is_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.capabilities.index'))->assertForbidden();
    }

    public function test_a_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.capabilities.index'))->assertRedirect(route('login'));
    }
}
