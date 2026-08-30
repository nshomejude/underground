<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\ProjectRecord;

/**
 * @extends Factory<ProjectRecord>
 */
final class ProjectRecordFactory extends Factory
{
    protected $model = ProjectRecord::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'slug' => Str::slug($title),
            'icon' => 'radar',
            'title' => $title,
            'sector' => 'Technology & Innovation',
            'body' => fake()->sentence(18),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
