<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\PortfolioEngagementRecord;

/**
 * @extends Factory<PortfolioEngagementRecord>
 */
final class PortfolioEngagementRecordFactory extends Factory
{
    protected $model = PortfolioEngagementRecord::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'slug' => Str::slug($title),
            'sector' => 'Finance & Investments',
            'title' => $title,
            'summary' => fake()->sentence(20),
            'outcome' => fake()->sentence(8),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
