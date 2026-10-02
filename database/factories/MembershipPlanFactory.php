<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MembershipPlan> */
final class MembershipPlanFactory extends Factory
{
    protected $model = MembershipPlan::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'tier_slug' => 'corporate-affiliate',
            'slug' => str($name)->slug()->toString(),
            'name' => ucfirst($name),
            'tagline' => fake()->sentence(6),
            'price_cents' => null,
            'currency' => 'USD',
            'billing_interval' => 'by_invitation',
            'features' => ['Member card and certificate'],
            'limits' => ['connection_requests_per_month' => 10],
            'is_featured' => false,
            'is_active' => true,
            'position' => 0,
        ];
    }
}
