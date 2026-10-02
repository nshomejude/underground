<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Motion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Motion> */
final class MotionFactory extends Factory
{
    protected $model = Motion::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'summary' => fake()->sentence(14),
            'body' => fake()->paragraph(),
            'kind' => 'decision',
            'min_tier_rank' => 1,
            'created_by' => User::factory(),
            'status' => Motion::OPEN,
            'choices' => Motion::DEFAULT_CHOICES,
            'quorum' => 1,
            'pass_threshold' => 50,
            'anonymous' => false,
            'weighted' => false,
            'allow_change' => true,
            'show_results' => 'after_close',
            'opens_at' => now()->subHour(),
            'closes_at' => now()->addDays(3),
            'published_at' => now()->subHour(),
        ];
    }

    public function draft(): self
    {
        return $this->state(['status' => Motion::DRAFT, 'published_at' => null]);
    }

    public function anonymous(): self
    {
        return $this->state(['anonymous' => true]);
    }
}
