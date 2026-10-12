<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\GalleryPhotoRecord;

/**
 * @extends Factory<GalleryPhotoRecord>
 */
final class GalleryPhotoRecordFactory extends Factory
{
    protected $model = GalleryPhotoRecord::class;

    public function definition(): array
    {
        $caption = fake()->sentence(4);

        return [
            'slug' => Str::slug($caption.'-'.fake()->unique()->numberBetween(1, 100000)),
            'caption' => $caption,
            'context' => fake()->sentence(10),
            'captured_on' => null,
            'photo_rights_note' => 'Cleared for site use by the firm.',
            'image_url' => null,
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
