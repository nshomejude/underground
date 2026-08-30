<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\EngagementModelRecord;

/**
 * @extends Factory<EngagementModelRecord>
 */
final class EngagementModelRecordFactory extends Factory
{
    protected $model = EngagementModelRecord::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($name),
            'name' => ucwords($name),
            'summary' => fake()->sentence(10),
            'icon' => fake()->randomElement(['handshake', 'target', 'shield-check', 'radar']),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
