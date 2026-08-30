<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\TeamMemberRecord;

/**
 * @extends Factory<TeamMemberRecord>
 */
final class TeamMemberRecordFactory extends Factory
{
    protected $model = TeamMemberRecord::class;

    public function definition(): array
    {
        $name = fake()->unique()->name();

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'title' => 'Partner, '.fake()->jobTitle(),
            'background' => fake()->sentence(12),
            'icon' => 'target',
            'has_portrait' => false,
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
