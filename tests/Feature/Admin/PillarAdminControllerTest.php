<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\PillarRecord;
use Tests\TestCase;

final class PillarAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.pillars.index'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_member_is_forbidden(): void
    {
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get(route('admin.pillars.index'))->assertForbidden();
    }

    public function test_an_admin_can_view_the_pillar_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        PillarRecord::factory()->create(['title' => 'Discreet', 'qualifier' => 'by Design']);

        $this->actingAs($admin)->get(route('admin.pillars.index'))
            ->assertOk()
            ->assertSee('Discreet');
    }

    public function test_an_admin_can_create_a_pillar(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.pillars.store'), [
            'title' => 'Global',
            'slug' => 'global-pillar',
            'qualifier' => 'by Reach',
            'icon' => 'globe',
            'position' => 4,
        ]);

        $response->assertRedirect(route('admin.pillars.index'));
        $this->assertDatabaseHas('pillars', ['slug' => 'global-pillar', 'title' => 'Global']);
    }

    public function test_creating_a_pillar_validates_required_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.pillars.store'), [
            'title' => '',
            'slug' => 'global-pillar',
            'qualifier' => '',
            'icon' => 'globe',
            'position' => 4,
        ]);

        $response->assertSessionHasErrors(['title', 'qualifier']);
        $this->assertDatabaseCount('pillars', 0);
    }

    public function test_an_admin_can_update_a_pillar(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        PillarRecord::factory()->create(['slug' => 'discreet-pillar', 'title' => 'Discreet']);

        $response = $this->actingAs($admin)->put(route('admin.pillars.update', 'discreet-pillar'), [
            'title' => 'Discreet',
            'slug' => 'discreet-pillar',
            'qualifier' => 'by Nature',
            'icon' => 'shield-check',
            'position' => 1,
        ]);

        $response->assertRedirect(route('admin.pillars.index'));
        $this->assertDatabaseHas('pillars', ['slug' => 'discreet-pillar', 'qualifier' => 'by Nature']);
    }

    public function test_an_admin_can_delete_a_pillar(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        PillarRecord::factory()->create(['slug' => 'discreet-pillar']);

        $response = $this->actingAs($admin)->delete(route('admin.pillars.destroy', 'discreet-pillar'));

        $response->assertRedirect(route('admin.pillars.index'));
        $this->assertDatabaseMissing('pillars', ['slug' => 'discreet-pillar']);
    }
}
