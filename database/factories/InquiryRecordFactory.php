<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\Engagement\ValueObjects\InquiryStatus;
use Domain\Engagement\ValueObjects\InterestArea;
use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Persistence\Eloquent\Models\InquiryRecord;

/**
 * @extends Factory<InquiryRecord>
 */
final class InquiryRecordFactory extends Factory
{
    protected $model = InquiryRecord::class;

    public function definition(): array
    {
        $submittedAt = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'reference' => sprintf(
                'UG-%s-%s',
                $submittedAt->format('Y'),
                strtoupper(fake()->bothify('??####')),
            ),
            'name' => fake()->name(),
            'organisation' => fake()->optional()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->e164PhoneNumber(),
            'country' => fake()->optional()->country(),
            'interest' => fake()->randomElement(InterestArea::cases())->value,
            'brief' => fake()->paragraph(),
            'status' => InquiryStatus::Received->value,
            'submitted_at' => $submittedAt,
        ];
    }
}
