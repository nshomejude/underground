<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;

/**
 * @extends Factory<CapabilityRecord>
 */
final class CapabilityRecordFactory extends Factory
{
    protected $model = CapabilityRecord::class;

    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();

        return [
            'slug' => Str::slug($title),
            'title' => $title,
            'summary' => fake()->sentence(12),
            'icon' => fake()->randomElement(['landmark', 'globe', 'radar', 'coins', 'handshake', 'megaphone', 'ship-wheel', 'library']),
            'position' => fake()->unique()->numberBetween(1, 100),
            'is_featured' => fake()->boolean(30),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true]);
    }
}
