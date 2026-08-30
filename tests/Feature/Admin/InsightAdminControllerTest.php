<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;
use Tests\TestCase;

final class InsightAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_list_insights(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        InsightRecord::factory()->create(['title' => 'Sahel Strategic Outlook']);

        $this->actingAs($admin)->get(route('admin.insights.index'))
            ->assertOk()
            ->assertSee('Sahel Strategic Outlook');
    }

    public function test_an_admin_can_create_an_insight(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.insights.store'), [
            'slug' => 'new-strategic-brief',
            'title' => 'New Strategic Brief',
            'category' => 'Geopolitics',
            'excerpt' => 'A brief look at emerging dynamics.',
            'body' => 'The full body of the insight goes here.',
            'published_at' => '',
        ]);

        $response->assertRedirect(route('admin.insights.index'));
        $this->assertDatabaseHas('insights', [
            'slug' => 'new-strategic-brief',
            'title' => 'New Strategic Brief',
            'published_at' => null,
        ]);
    }

    public function test_creating_an_insight_validates_required_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.insights.store'), [
            'slug' => 'incomplete',
        ]);

        $response->assertSessionHasErrors(['title', 'category', 'excerpt', 'body']);
        $this->assertDatabaseMissing('insights', ['slug' => 'incomplete']);
    }

    public function test_an_admin_can_edit_an_insight(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $insight = InsightRecord::factory()->create(['slug' => 'existing-insight', 'title' => 'Original Title']);

        $this->actingAs($admin)->get(route('admin.insights.edit', $insight->slug))
            ->assertOk()
            ->assertSee('Original Title');

        $response = $this->actingAs($admin)->put(route('admin.insights.update', $insight->slug), [
            'title' => 'Updated Title',
            'category' => $insight->category,
            'excerpt' => $insight->excerpt,
            'body' => $insight->body,
            'published_at' => '',
        ]);

        $response->assertRedirect(route('admin.insights.index'));
        $this->assertDatabaseHas('insights', ['slug' => 'existing-insight', 'title' => 'Updated Title']);
    }

    public function test_an_admin_can_delete_an_insight_without_affecting_capabilities(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $insight = InsightRecord::factory()->create();
        CapabilityRecord::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.insights.destroy', $insight->slug));

        $response->assertRedirect(route('admin.insights.index'));
        $this->assertDatabaseMissing('insights', ['slug' => $insight->slug]);
        $this->assertDatabaseCount('capabilities', 1);
    }

    public function test_a_non_admin_is_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.insights.index'))->assertForbidden();
    }

    public function test_a_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.insights.index'))->assertRedirect(route('login'));
    }
}
