<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\PartnerCategoryRecord;

/**
 * @extends Factory<PartnerCategoryRecord>
 */
final class PartnerCategoryRecordFactory extends Factory
{
    protected $model = PartnerCategoryRecord::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'icon' => 'briefcase',
            'title' => ucwords($title),
            'body' => fake()->sentence(15),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
