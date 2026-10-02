<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MembershipPlan;
use App\Models\PlanChangeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PlanChangeRequest> */
final class PlanChangeRequestFactory extends Factory
{
    protected $model = PlanChangeRequest::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'to_plan_id' => MembershipPlan::factory(),
            'status' => 'pending',
            'note' => null,
        ];
    }
}
