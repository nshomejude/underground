<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Motion;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Vote> */
final class VoteFactory extends Factory
{
    protected $model = Vote::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'motion_id' => Motion::factory(),
            'user_id' => User::factory(),
            'choice' => 'For',
            'weight' => 1,
        ];
    }
}
