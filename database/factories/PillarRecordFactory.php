<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\PillarRecord;

/**
 * @extends Factory<PillarRecord>
 */
final class PillarRecordFactory extends Factory
{
    protected $model = PillarRecord::class;

    public function definition(): array
    {
        $title = fake()->unique()->word();

        return [
            'slug' => Str::slug($title.'-pillar'),
            'title' => ucfirst($title),
            'qualifier' => fake()->randomElement(['by Design', 'by Nature', 'by Execution', 'by Reach']),
            'icon' => fake()->randomElement(['shield-check', 'target', 'radar', 'globe']),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
