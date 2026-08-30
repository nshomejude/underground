<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\NarrativeRecord;

/**
 * Seeds the single row of authored brand copy from the content brief.
 * The repository always resolves "current" as the latest row, so seeding
 * is idempotent by updating the one row keyed on company name.
 */
final class NarrativeSeeder extends Seeder
{
    public function run(): void
    {
        NarrativeRecord::query()->updateOrCreate(
            ['company' => 'Underground'],
            [
                'tagline' => 'Strategic Influence. Real Outcomes.',
                'eyebrow' => 'Underground',
                'headline' => ['Power Beneath', 'the Surface.'],
                'accent_line' => 'Discreet. Strategic. Effective. We operate where decisions are made and outcomes are shaped.',
                'intro' => 'Underground is a global strategic advisory and influence firm operating at the intersection of politics, government, business, capital, and media. We move in the shadows so our clients can lead in the light.',
                'primary_cta' => ['label' => 'Start a confidential conversation', 'href' => '#inquiry'],
                'secondary_cta' => ['label' => 'Explore capabilities', 'href' => '#capabilities'],
                'creed_title' => 'Discreet. Strategic. Effective.',
                'creed_body' => 'We operate where decisions are made and outcomes are shaped.',
                'capabilities_eyebrow' => 'Capabilities',
                'capabilities_heading' => 'Where we bring our influence to bear.',
                'sectors_heading' => 'Sectors we operate in.',
                'reach_heading' => 'A global reach, built over decades.',
                'reach_body' => 'Our relationships span governments, capital, and industry across every region where decisions are made.',
                'reach_cta' => ['label' => 'View our reach', 'href' => '#reach'],
                'engagement_heading' => 'How clients retain us.',
                'closing_heading' => 'Power Beneath the Surface.',
                'closing_support' => 'Discreet. Strategic. Effective. We operate where decisions are made and outcomes are shaped.',
                'closing_cta' => ['label' => 'Start a confidential conversation', 'href' => '#inquiry'],
                'navigation' => [
                    ['label' => 'Capabilities', 'href' => '#capabilities'],
                    ['label' => 'Sectors', 'href' => '#sectors'],
                    ['label' => 'Insights', 'href' => '#insights'],
                ],
                'copyright' => sprintf('© %d Underground. All rights reserved.', now()->year),
            ],
        );
    }
}
