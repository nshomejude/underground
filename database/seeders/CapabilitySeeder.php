<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;

/**
 * Seeds the eight disciplines Underground sells, from the content brief.
 * The first three are featured on the landing page.
 */
final class CapabilitySeeder extends Seeder
{
    public function run(): void
    {
        $capabilities = [
            [
                'slug' => 'government-political-affairs',
                'title' => 'Government & Political Affairs',
                'summary' => 'Direct access to the officials and institutions shaping policy, regulation, and public investment.',
                'icon' => 'landmark',
                'position' => 1,
                'is_featured' => true,
            ],
            [
                'slug' => 'international-relations-diplomacy',
                'title' => 'International Relations & Diplomacy',
                'summary' => 'Navigating bilateral and multilateral relationships across contested and emerging geographies.',
                'icon' => 'globe',
                'position' => 2,
                'is_featured' => true,
            ],
            [
                'slug' => 'strategic-intelligence-analysis',
                'title' => 'Strategic Intelligence & Analysis',
                'summary' => 'Grounded, real-time reads on the political, economic, and security dynamics that shape a decision.',
                'icon' => 'radar',
                'position' => 3,
                'is_featured' => true,
            ],
            [
                'slug' => 'investment-capital-strategy',
                'title' => 'Investment & Capital Strategy',
                'summary' => 'Structuring capital and investment strategy alongside the political and regulatory reality on the ground.',
                'icon' => 'coins',
                'position' => 4,
                'is_featured' => false,
            ],
            [
                'slug' => 'business-development-partnerships',
                'title' => 'Business Development & Partnerships',
                'summary' => 'Building the commercial relationships and partnerships that turn strategy into a durable market position.',
                'icon' => 'handshake',
                'position' => 5,
                'is_featured' => false,
            ],
            [
                'slug' => 'media-narrative-management',
                'title' => 'Media & Narrative Management',
                'summary' => 'Shaping the public narrative around a client, a deal, or a moment before it shapes them.',
                'icon' => 'megaphone',
                'position' => 6,
                'is_featured' => false,
            ],
            [
                'slug' => 'ppp-infrastructure-advisory',
                'title' => 'PPP & Infrastructure Advisory',
                'summary' => 'Structuring public-private partnerships and infrastructure mandates from concept through execution.',
                'icon' => 'ship-wheel',
                'position' => 7,
                'is_featured' => false,
            ],
            [
                'slug' => 'think-tank-strategic-advisory',
                'title' => 'Think Tank & Strategic Advisory',
                'summary' => 'Long-horizon strategic counsel drawing on a network of policy, security, and economic experts.',
                'icon' => 'library',
                'position' => 8,
                'is_featured' => false,
            ],
        ];

        foreach ($capabilities as $capability) {
            CapabilityRecord::query()->updateOrCreate(
                ['slug' => $capability['slug']],
                [
                    'title' => $capability['title'],
                    'summary' => $capability['summary'],
                    'icon' => $capability['icon'],
                    'position' => $capability['position'],
                    'is_featured' => $capability['is_featured'],
                ],
            );
        }
    }
}
