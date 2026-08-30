<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Infrastructure\Persistence\Eloquent\Models\NarrativeRecord;

/**
 * @extends Factory<NarrativeRecord>
 */
final class NarrativeRecordFactory extends Factory
{
    protected $model = NarrativeRecord::class;

    public function definition(): array
    {
        return [
            'company' => 'Underground',
            'tagline' => 'Strategic Influence. Real Outcomes.',
            'eyebrow' => fake()->words(2, true),
            'headline' => ['Power Beneath', 'the Surface.'],
            'accent_line' => fake()->sentence(8),
            'intro' => fake()->paragraph(),
            'primary_cta' => ['label' => 'Start a confidential conversation', 'href' => '#inquiry'],
            'secondary_cta' => ['label' => 'Explore capabilities', 'href' => '#capabilities'],
            'creed_title' => fake()->sentence(4),
            'creed_body' => fake()->paragraph(),
            'capabilities_eyebrow' => fake()->words(2, true),
            'capabilities_heading' => fake()->sentence(5),
            'sectors_heading' => fake()->sentence(5),
            'reach_heading' => fake()->sentence(5),
            'reach_body' => fake()->paragraph(),
            'reach_cta' => ['label' => 'View our reach', 'href' => '#reach'],
            'engagement_heading' => fake()->sentence(5),
            'closing_heading' => fake()->sentence(5),
            'closing_support' => fake()->paragraph(),
            'closing_cta' => ['label' => 'Start a confidential conversation', 'href' => '#inquiry'],
            'navigation' => [
                ['label' => 'Capabilities', 'href' => '#capabilities'],
                ['label' => 'Sectors', 'href' => '#sectors'],
                ['label' => 'Insights', 'href' => '#insights'],
            ],
            'copyright' => sprintf('© %d Underground. All rights reserved.', now()->year),
        ];
    }
}
