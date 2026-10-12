<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\MediaItemRecord;

/**
 * @extends Factory<MediaItemRecord>
 */
final class MediaItemRecordFactory extends Factory
{
    protected $model = MediaItemRecord::class;

    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();

        return [
            'slug' => Str::slug($title),
            'type' => fake()->randomElement(['livestream', 'podcast', 'magazine']),
            'title' => $title,
            'description' => fake()->sentence(15),
            'date' => fake()->date(),
            'speaker' => fake()->name(),
            'status' => fake()->randomElement(['upcoming', 'published', 'archived']),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
