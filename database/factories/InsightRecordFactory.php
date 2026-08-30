<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;

/**
 * @extends Factory<InsightRecord>
 */
final class InsightRecordFactory extends Factory
{
    protected $model = InsightRecord::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'slug' => Str::slug($title),
            'title' => $title,
            'category' => fake()->randomElement(['Geopolitics', 'Government Affairs', 'Infrastructure', 'Strategy']),
            'excerpt' => fake()->sentence(20),
            'body' => implode("\n\n", fake()->paragraphs(5)),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => ['published_at' => null]);
    }
}
