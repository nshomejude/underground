<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\Consultation\ValueObjects\BudgetRange;
use Domain\Consultation\ValueObjects\ConsultationReference;
use Domain\Consultation\ValueObjects\ConsultationSector;
use Domain\Consultation\ValueObjects\ConsultationStatus;
use Domain\Consultation\ValueObjects\MaturityStage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Persistence\Eloquent\Models\ConsultationRequestRecord;

/**
 * @extends Factory<ConsultationRequestRecord>
 */
final class ConsultationRequestRecordFactory extends Factory
{
    protected $model = ConsultationRequestRecord::class;

    public function definition(): array
    {
        $submittedAt = fake()->dateTimeBetween('-6 months', 'now');

        return [
            // Built through the real value object rather than a hand-rolled
            // pattern, so it always matches ConsultationReference's strict
            // alphabet (no 0/1/I/O — see its class doc).
            'reference' => ConsultationReference::generate((int) $submittedAt->format('Y'))->value,
            'name' => fake()->name(),
            'organisation' => fake()->optional()->company(),
            'role' => fake()->optional()->jobTitle(),
            'email' => fake()->unique()->safeEmail(),
            'country' => fake()->optional()->country(),
            'sector' => fake()->randomElement(ConsultationSector::cases())->value,
            'maturity_stage' => fake()->randomElement(MaturityStage::cases())->value,
            'project_description' => fake()->paragraph(),
            'need_description' => fake()->paragraph(),
            'budget_range' => fake()->randomElement(BudgetRange::cases())->value,
            'has_supporting_documents' => fake()->boolean(),
            'status' => ConsultationStatus::Submitted->value,
            'submitted_at' => $submittedAt,
        ];
    }
}
