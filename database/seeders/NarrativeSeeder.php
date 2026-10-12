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
                'tagline' => 'Underground Network — Power Beneath the Surface',
                'eyebrow' => 'Underground Network',
                'headline' => ['Power Beneath', 'the Surface.'],
                'accent_line' => 'Discreet. Prepared. Confidential. We support institutions, companies, and project sponsors from Washington.',
                'intro' => 'Underground Network is a strategic advisory and economic-diplomacy firm operating from Washington, D.C. We help institutions, companies, and project sponsors analyze, prepare, and coordinate the international partnerships and defined missions that matter most, with a particular focus on Africa.',
                'primary_cta' => ['label' => 'Discover Our Expertise', 'href' => '#capabilities'],
                'secondary_cta' => ['label' => 'Submit a Request', 'href' => '#inquiry'],
                'creed_title' => 'Discreet. Strategic. Effective.',
                'creed_body' => 'We operate from Washington, where strategic decisions are made and defined missions are prepared.',
                'capabilities_eyebrow' => 'What We Do',
                'capabilities_heading' => 'Expertise That Shapes Outcomes',
                'sectors_heading' => 'Sectors We Serve',
                'reach_heading' => 'Global Reach. Local Insight.',
                'reach_body' => 'With a global network of offices, partners, and trusted relationships, we operate where decisions are made and opportunities emerge, with particular depth across Africa.',
                'reach_cta' => ['label' => 'Our Global Presence', 'href' => '#reach'],
                'engagement_heading' => 'Engagement Models',
                'closing_heading' => "Let's Build What Others Think Is Impossible.",
                'closing_support' => 'Discreet conversations. Extraordinary outcomes.',
                'closing_cta' => ['label' => 'Start a Confidential Conversation', 'href' => '#inquiry'],
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
