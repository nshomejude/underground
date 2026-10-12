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
            'category' => fake()->randomElement(['Geopolitics', 'Government Affairs', 'Infrastructure', 'Strategic Notes']),
            'excerpt' => fake()->sentence(20),
            'body' => implode("\n\n", fake()->paragraphs(5)),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'author' => fake()->randomElement([
                'Tony Smith', 'Naledi Okonjo-Reyes', 'Marcus Thane', 'Yumi Castellanos',
            ]),
            'country' => fake()->randomElement(['Global', 'United States', 'United Kingdom']),
            'sector' => fake()->randomElement([
                'Government & Public Sector', 'Energy & Natural Resources',
                'Infrastructure & Transportation', 'Finance & Investments',
            ]),
            'access_level' => fake()->randomElement(['public', 'members']),
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => ['published_at' => null]);
    }
}
