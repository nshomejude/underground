<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\OfficeRecord;

/**
 * @extends Factory<OfficeRecord>
 */
final class OfficeRecordFactory extends Factory
{
    protected $model = OfficeRecord::class;

    public function definition(): array
    {
        $name = fake()->unique()->city().' Office';

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'region' => fake()->country(),
            'address' => null,
            'email' => null,
            'phone' => null,
            'blurb' => fake()->sentence(12),
            'lat' => null,
            'lng' => null,
            'countries' => [fake()->country()],
            'status' => 'partner_coverage',
            'relationship_type' => 'partnership',
            'is_headquarters' => false,
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
