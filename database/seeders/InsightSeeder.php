<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\InsightRecord;

/**
 * Seeds the four sample published thought-leadership pieces from the
 * content brief, most recent first.
 */
final class InsightSeeder extends Seeder
{
    public function run(): void
    {
        $insights = [
            [
                'slug' => 'discretion-as-strategy',
                'title' => 'Discretion as Strategy: Why the Quietest Firms Win',
                'category' => 'Strategy',
                'excerpt' => 'The firms that shape the most outcomes are rarely the ones you read about — discretion is not the absence of influence, it is the mechanism of it.',
                'published_at_offset' => '-3 weeks',
            ],
            [
                'slug' => 'infrastructure-as-influence',
                'title' => 'Infrastructure as Influence: The New Great Game',
                'category' => 'Infrastructure',
                'excerpt' => 'Ports, corridors, and grids are no longer just capital projects — they are the instruments through which states and sponsors compete for lasting influence.',
                'published_at_offset' => '-6 weeks',
            ],
            [
                'slug' => 'government-affairs-multipolar-world',
                'title' => 'Government Affairs in a Multipolar World',
                'category' => 'Government Affairs',
                'excerpt' => 'As power diffuses across more capitals, effective government affairs demands a wider map of relationships and a faster read on where decisions actually get made.',
                'published_at_offset' => '-10 weeks',
            ],
            [
                'slug' => 'geopolitical-realignment-capital-flows',
                'title' => 'The Realignment of Global Capital Flows in an Era of Strategic Competition',
                'category' => 'Geopolitics',
                'excerpt' => 'Capital is following strategic alignment as much as it follows yield — understanding that shift is now a precondition for structuring durable investment.',
                'published_at_offset' => '-4 months',
            ],
        ];

        foreach ($insights as $insight) {
            InsightRecord::query()->updateOrCreate(
                ['slug' => $insight['slug']],
                [
                    'title' => $insight['title'],
                    'category' => $insight['category'],
                    'excerpt' => $insight['excerpt'],
                    'body' => $this->body($insight['title']),
                    'published_at' => now()->modify($insight['published_at_offset']),
                ],
            );
        }
    }

    private function body(string $title): string
    {
        return <<<BODY
        {$title}

        Underground operates at the intersection of politics, government, business, capital, and media. This piece
        reflects the kind of strategic thinking our practice brings to clients navigating complex, high-stakes
        environments — discreet counsel grounded in real relationships and real outcomes.

        Our advisory work is never theoretical. It is built on decades of experience inside the rooms where
        decisions are actually made, and it is delivered with the discretion our clients require.
        BODY;
    }
}
