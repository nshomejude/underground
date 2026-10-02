<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Connection>
 */
final class ConnectionFactory extends Factory
{
    protected $model = Connection::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'addressee_id' => User::factory(),
            'kind' => 'connect',
            'status' => 'pending',
            'message' => fake()->sentence(12),
        ];
    }

    public function between(User $requester, User $addressee): static
    {
        return $this->state(['requester_id' => $requester->id, 'addressee_id' => $addressee->id]);
    }

    public function collaboration(string $topic = 'Joint venture exploration'): static
    {
        return $this->state(['kind' => 'collaborate', 'topic' => $topic]);
    }

    public function accepted(): static
    {
        return $this->state(['status' => 'accepted', 'responded_at' => now()]);
    }

    public function declined(?\DateTimeInterface $at = null): static
    {
        return $this->state(['status' => 'declined', 'responded_at' => $at ?? now()]);
    }

    public function blockedBy(User $blocker): static
    {
        return $this->state(['status' => 'blocked', 'blocked_by' => $blocker->id, 'responded_at' => now()]);
    }
}
