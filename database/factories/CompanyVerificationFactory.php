<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CompanyVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompanyVerification> */
final class CompanyVerificationFactory extends Factory
{
    protected $model = CompanyVerification::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => 'draft',
            'last_step' => 1,
            'company_name' => fake()->company(),
            'registration_number' => 'RC'.fake()->numerify('######'),
            'country' => 'NG',
            'incorporation_date' => now()->subYears(6)->toDateString(),
            'registered_address' => fake()->address(),
            'applicant_role' => 'Director',
            'sectors' => ['technology-innovation'],
            'consent_at' => now(),
            'consent_version' => config('verification.consent_version'),
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => ['status' => 'submitted', 'last_step' => 4, 'submitted_at' => now()]);
    }

    public function inReview(): static
    {
        return $this->state(fn () => ['status' => 'in_review', 'last_step' => 4, 'submitted_at' => now()->subDay(), 'in_review_at' => now()]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved', 'last_step' => 4, 'submitted_at' => now()->subDays(2),
            'reviewed_at' => now(), 'expires_at' => now()->addMonths(24),
        ]);
    }
}
