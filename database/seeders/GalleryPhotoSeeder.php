<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * There are no real, rights-cleared photos to publish on /team/gallery
 * yet, so this seeder intentionally seeds nothing — see
 * Domain\Content\Entities\GalleryPhoto's class doc and
 * resources/views/team/gallery.blade.php's empty state. An admin adds
 * genuine photos later via Admin\GalleryPhotoAdminController.
 */
final class GalleryPhotoSeeder extends Seeder
{
    public function run(): void
    {
        // Deliberately empty.
    }
}
