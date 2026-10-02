<?php

namespace Database\Factories;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Conversation> */
class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'connection_id' => fn () => Connection::query()->create([
                'requester_id' => User::factory()->create()->id,
                'addressee_id' => User::factory()->create()->id,
                'status' => 'accepted',
                'responded_at' => now(),
            ])->id,
        ];
    }
}
