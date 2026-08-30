<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\MetricRecord;
use Tests\TestCase;

final class MetricAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.metrics.index'))->assertRedirect(route('login'));
    }

    public function test_a_non_admin_member_is_forbidden(): void
    {
        $member = User::factory()->create(['is_admin' => false]);

        $this->actingAs($member)->get(route('admin.metrics.index'))->assertForbidden();
    }

    public function test_an_admin_can_view_the_metric_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        MetricRecord::factory()->create(['value' => '250+', 'label' => 'government relationships']);

        $this->actingAs($admin)->get(route('admin.metrics.index'))
            ->assertOk()
            ->assertSee('250+');
    }

    public function test_an_admin_can_create_a_metric(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.metrics.store'), [
            'value' => '90+',
            'label' => 'countries served',
            'slug' => 'countries-served',
            'icon' => 'globe',
            'position' => 4,
        ]);

        $response->assertRedirect(route('admin.metrics.index'));
        $this->assertDatabaseHas('metrics', ['slug' => 'countries-served', 'value' => '90+']);
    }

    public function test_creating_a_metric_validates_the_icon_against_the_whitelist(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.metrics.store'), [
            'value' => '90+',
            'label' => 'countries served',
            'slug' => 'countries-served',
            'icon' => 'not-a-real-icon',
            'position' => 4,
        ]);

        $response->assertSessionHasErrors(['icon']);
        $this->assertDatabaseCount('metrics', 0);
    }

    public function test_an_admin_can_update_a_metric(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        MetricRecord::factory()->create(['slug' => 'countries-served', 'value' => '80+']);

        $response = $this->actingAs($admin)->put(route('admin.metrics.update', 'countries-served'), [
            'value' => '90+',
            'label' => 'countries served',
            'slug' => 'countries-served',
            'icon' => 'globe',
            'position' => 1,
        ]);

        $response->assertRedirect(route('admin.metrics.index'));
        $this->assertDatabaseHas('metrics', ['slug' => 'countries-served', 'value' => '90+']);
    }

    public function test_an_admin_can_delete_a_metric(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        MetricRecord::factory()->create(['slug' => 'countries-served']);

        $response = $this->actingAs($admin)->delete(route('admin.metrics.destroy', 'countries-served'));

        $response->assertRedirect(route('admin.metrics.index'));
        $this->assertDatabaseMissing('metrics', ['slug' => 'countries-served']);
    }
}
