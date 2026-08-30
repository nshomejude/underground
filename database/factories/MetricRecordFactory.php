<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\MetricRecord;

/**
 * @extends Factory<MetricRecord>
 */
final class MetricRecordFactory extends Factory
{
    protected $model = MetricRecord::class;

    public function definition(): array
    {
        $label = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($label),
            'value' => fake()->randomElement(['50+', '100+', '250+', '$1B+', '$20B+', '100%']),
            'label' => ucwords($label),
            'icon' => fake()->randomElement(['globe', 'handshake', 'flag', 'landmark', 'shield-check']),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
