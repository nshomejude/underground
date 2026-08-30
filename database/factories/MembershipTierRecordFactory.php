<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;

/**
 * @extends Factory<MembershipTierRecord>
 */
final class MembershipTierRecordFactory extends Factory
{
    protected $model = MembershipTierRecord::class;

    public function definition(): array
    {
        $name = fake()->unique()->catchPhrase();

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'audience' => fake()->sentence(6),
            'icon' => fake()->randomElement(['landmark', 'gem', 'building-2']),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
