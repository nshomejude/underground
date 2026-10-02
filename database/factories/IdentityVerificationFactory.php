<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\IdentityVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<IdentityVerification> */
final class IdentityVerificationFactory extends Factory
{
    protected $model = IdentityVerification::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => 'draft',
            'last_step' => 1,
            'document_type' => 'passport',
            'document_country' => 'GB',
            'document_number_last4' => '4821',
            'full_name_on_document' => fake()->name(),
            'date_of_birth_encrypted' => encrypt('1985-04-12'),
            'document_expiry' => now()->addYears(4)->toDateString(),
            'consent_at' => now(),
            'consent_version' => config('verification.consent_version'),
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => ['status' => 'submitted', 'last_step' => 5, 'submitted_at' => now()]);
    }

    public function inReview(): static
    {
        return $this->state(fn () => ['status' => 'in_review', 'last_step' => 5, 'submitted_at' => now()->subDay(), 'in_review_at' => now()]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved', 'last_step' => 5, 'submitted_at' => now()->subDays(2),
            'reviewed_at' => now(), 'expires_at' => now()->addMonths(24),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected', 'last_step' => 5, 'submitted_at' => now()->subDays(2),
            'reviewed_at' => now(), 'rejection_reason' => 'Document unreadable or too low quality',
        ]);
    }
}
