<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\SectorRecord;

/**
 * @extends Factory<SectorRecord>
 */
final class SectorRecordFactory extends Factory
{
    protected $model = SectorRecord::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($name),
            'name' => ucwords($name),
            'summary' => fake()->sentence(12),
            'motif' => fake()->randomElement(['skyline', 'grid', 'harbour', 'satellite', 'ledger', 'circuit']),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
