<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\SectorRecord;
use Tests\TestCase;

final class SectorAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.sectors.index'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_member_is_forbidden(): void
    {
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get(route('admin.sectors.index'))->assertForbidden();
    }

    public function test_an_admin_can_view_the_sector_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        SectorRecord::factory()->create(['name' => 'Oil & Gas']);

        $this->actingAs($admin)->get(route('admin.sectors.index'))
            ->assertOk()
            ->assertSee('Oil & Gas');
    }

    public function test_an_admin_can_create_a_sector(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.sectors.store'), [
            'name' => 'Aerospace & Defence',
            'slug' => 'aerospace-defence',
            'summary' => 'Prime contractors and defence ministries.',
            'motif' => 'radar',
            'position' => 5,
        ]);

        $response->assertRedirect(route('admin.sectors.index'));
        $this->assertDatabaseHas('sectors', ['slug' => 'aerospace-defence', 'name' => 'Aerospace & Defence']);
    }

    public function test_creating_a_sector_validates_required_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.sectors.store'), [
            'name' => '',
            'slug' => 'Invalid Slug!',
            'summary' => '',
            'motif' => '',
            'position' => 1,
        ]);

        $response->assertSessionHasErrors(['name', 'slug', 'summary', 'motif']);
        $this->assertDatabaseCount('sectors', 0);
    }

    public function test_an_admin_can_view_the_edit_form(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        SectorRecord::factory()->create(['slug' => 'oil-gas', 'name' => 'Oil & Gas']);

        $this->actingAs($admin)->get(route('admin.sectors.edit', 'oil-gas'))
            ->assertOk()
            ->assertSee('Oil & Gas');
    }

    public function test_an_admin_can_update_a_sector(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        SectorRecord::factory()->create(['slug' => 'oil-gas', 'name' => 'Oil & Gas', 'position' => 1]);

        $response = $this->actingAs($admin)->put(route('admin.sectors.update', 'oil-gas'), [
            'name' => 'Oil, Gas & Energy',
            'slug' => 'oil-gas',
            'summary' => 'Updated summary text.',
            'motif' => 'skyline',
            'position' => 2,
        ]);

        $response->assertRedirect(route('admin.sectors.index'));
        $this->assertDatabaseHas('sectors', ['slug' => 'oil-gas', 'name' => 'Oil, Gas & Energy', 'position' => 2]);
    }

    public function test_an_admin_can_rename_a_sectors_slug(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        SectorRecord::factory()->create(['slug' => 'old-slug', 'name' => 'Old Name']);

        $this->actingAs($admin)->put(route('admin.sectors.update', 'old-slug'), [
            'name' => 'New Name',
            'slug' => 'new-slug',
            'summary' => 'Renamed sector summary.',
            'motif' => 'grid',
            'position' => 1,
        ]);

        $this->assertDatabaseMissing('sectors', ['slug' => 'old-slug']);
        $this->assertDatabaseHas('sectors', ['slug' => 'new-slug', 'name' => 'New Name']);
        $this->assertDatabaseCount('sectors', 1);
    }

    public function test_an_admin_can_delete_a_sector(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        SectorRecord::factory()->create(['slug' => 'oil-gas']);

        $response = $this->actingAs($admin)->delete(route('admin.sectors.destroy', 'oil-gas'));

        $response->assertRedirect(route('admin.sectors.index'));
        $this->assertDatabaseMissing('sectors', ['slug' => 'oil-gas']);
    }
}
