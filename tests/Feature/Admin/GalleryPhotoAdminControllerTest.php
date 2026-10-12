<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\GalleryPhotoRecord;
use Tests\Concerns\RegistersTeamRoutes;
use Tests\TestCase;

final class GalleryPhotoAdminControllerTest extends TestCase
{
    use RefreshDatabase;
    use RegistersTeamRoutes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerTeamRoutes();
    }

    public function test_an_admin_can_list_photos(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        GalleryPhotoRecord::factory()->create(['caption' => 'A Gallery Photo']);

        $this->actingAs($admin)->get(route('admin.gallery.index'))
            ->assertOk()
            ->assertSee('A Gallery Photo');
    }

    public function test_an_admin_can_create_a_photo(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'slug' => 'new-photo',
            'caption' => 'New Photo',
            'context' => 'A delegation visit.',
            'captured_on' => 'March 2026',
            'photo_rights_note' => 'Consent given by all pictured.',
            'image_url' => 'https://example.com/photo.jpg',
            'position' => 1,
        ]);

        $response->assertRedirect(route('admin.gallery.index'));
        $this->assertDatabaseHas('gallery_photos', [
            'slug' => 'new-photo',
            'caption' => 'New Photo',
            'image_url' => 'https://example.com/photo.jpg',
        ]);
    }

    public function test_creating_a_photo_requires_a_photo_rights_note(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'slug' => 'missing-rights',
            'caption' => 'Missing Rights',
            'position' => 1,
        ]);

        $response->assertSessionHasErrors(['photo_rights_note']);
        $this->assertDatabaseMissing('gallery_photos', ['slug' => 'missing-rights']);
    }

    public function test_an_admin_can_delete_a_photo(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $photo = GalleryPhotoRecord::factory()->create(['slug' => 'to-delete']);

        $this->actingAs($admin)->delete(route('admin.gallery.destroy', $photo->slug))
            ->assertRedirect(route('admin.gallery.index'));

        $this->assertDatabaseMissing('gallery_photos', ['slug' => 'to-delete']);
    }
}
