<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\EventRecord;

/**
 * @extends Factory<EventRecord>
 */
final class EventRecordFactory extends Factory
{
    protected $model = EventRecord::class;

    public function definition(): array
    {
        $name = fake()->unique()->catchPhrase();

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'date' => fake()->date(),
            'location' => fake()->city(),
            'description' => fake()->sentence(15),
            'position' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
