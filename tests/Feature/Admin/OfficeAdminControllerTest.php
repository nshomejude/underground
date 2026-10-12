<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\OfficeRecord;
use Tests\Concerns\RegistersGlobalReachRoutes;
use Tests\TestCase;

final class OfficeAdminControllerTest extends TestCase
{
    use RefreshDatabase;
    use RegistersGlobalReachRoutes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerGlobalReachRoutes();
    }

    public function test_an_admin_can_list_offices(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        OfficeRecord::factory()->create(['name' => 'Gulf Regional Office']);

        $this->actingAs($admin)->get(route('admin.offices.index'))
            ->assertOk()
            ->assertSee('Gulf Regional Office');
    }

    public function test_an_admin_can_create_an_office(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.offices.store'), [
            'slug' => 'new-office',
            'name' => 'New Office',
            'region' => 'Somewhere',
            'blurb' => 'A new coverage entry.',
            'countries_text' => "Country One\nCountry Two\n\n",
            'status' => 'partner_coverage',
            'relationship_type' => 'partnership',
            'position' => 5,
        ]);

        $response->assertRedirect(route('admin.offices.index'));
        $this->assertDatabaseHas('offices', [
            'slug' => 'new-office',
            'name' => 'New Office',
            'status' => 'partner_coverage',
            'relationship_type' => 'partnership',
        ]);

        $office = OfficeRecord::query()->where('slug', 'new-office')->first();
        $this->assertSame(['Country One', 'Country Two'], $office->countries);
    }

    public function test_creating_an_office_validates_required_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.offices.store'), [
            'slug' => 'incomplete',
        ]);

        $response->assertSessionHasErrors(['name', 'region', 'blurb', 'status', 'relationship_type', 'position']);
        $this->assertDatabaseMissing('offices', ['slug' => 'incomplete']);
    }

    public function test_creating_an_office_rejects_a_status_outside_the_enum(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.offices.store'), [
            'slug' => 'bad-status',
            'name' => 'Bad Status',
            'region' => 'Somewhere',
            'blurb' => 'A blurb.',
            'status' => 'definitely-owned-branch',
            'relationship_type' => 'partnership',
            'position' => 1,
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    public function test_an_admin_can_edit_an_office(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $office = OfficeRecord::factory()->create(['slug' => 'existing-office', 'name' => 'Original Name']);

        $this->actingAs($admin)->get(route('admin.offices.edit', $office->slug))
            ->assertOk()
            ->assertSee('Original Name');

        $response = $this->actingAs($admin)->put(route('admin.offices.update', $office->slug), [
            'slug' => $office->slug,
            'name' => 'Updated Name',
            'region' => $office->region,
            'blurb' => $office->blurb,
            'countries_text' => implode("\n", $office->countries),
            'status' => $office->status,
            'relationship_type' => $office->relationship_type,
            'position' => $office->position,
        ]);

        $response->assertRedirect(route('admin.offices.index'));
        $this->assertDatabaseHas('offices', ['slug' => 'existing-office', 'name' => 'Updated Name']);
    }

    public function test_an_admin_can_delete_an_office(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $office = OfficeRecord::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.offices.destroy', $office->slug));

        $response->assertRedirect(route('admin.offices.index'));
        $this->assertDatabaseMissing('offices', ['slug' => $office->slug]);
    }

    public function test_a_non_admin_is_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.offices.index'))->assertForbidden();
    }

    public function test_a_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.offices.index'))->assertRedirect(route('login'));
    }
}
