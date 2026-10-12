<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\MediaItemRecord;

/**
 * Seeds Flex Media Group — the firm's livestream, podcast, and Underground
 * Magazine output — shown on /flex-media-group. Speakers named below are
 * existing Underground partners (see TeamMemberSeeder); no guest, client,
 * or outcome is named or implied beyond what is stated here.
 */
final class MediaItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Livestream
            [
                'type' => 'livestream',
                'title' => 'Underground Live: The Year-End Outlook',
                'description' => 'A live year-end briefing on the political and capital currents we expect to shape the first half of next year.',
                'date' => '2026-12-09',
                'speaker' => 'Tony Smith, Founder & Managing Partner',
                'status' => 'upcoming',
            ],
            [
                'type' => 'livestream',
                'title' => 'Capital Flows & Political Risk, Live',
                'description' => 'A live conversation on how institutional allocators are weighing political risk against return in the current cycle.',
                'date' => '2026-09-16',
                'speaker' => 'Yumi Castellanos, Partner, Investment & Capital Strategy',
                'status' => 'published',
            ],
            [
                'type' => 'livestream',
                'title' => 'Reading an Election From the Outside',
                'description' => 'A livestream walkthrough of how we read an unfolding election cycle as outside advisors, not participants.',
                'date' => '2026-06-24',
                'speaker' => 'Naledi Okonjo-Reyes, Partner, Government & Political Affairs',
                'status' => 'published',
            ],
            [
                'type' => 'livestream',
                'title' => 'After the Handshake: A Transition Debrief',
                'description' => 'A recorded debrief on what changes, and what should not, inside an institution once a political transition is formally complete.',
                'date' => '2025-11-04',
                'speaker' => 'Marcus Thane, Partner, Strategic Intelligence & Analysis',
                'status' => 'archived',
            ],

            // Podcast
            [
                'type' => 'podcast',
                'title' => 'Submarine Diplomacy: The Discreet Channel',
                'description' => 'Submarine Diplomacy is our term for a prepared, confidential channel: a carefully structured conversation between principals who need to speak candidly before anything is made public. This episode walks through the method using a hypothetical, illustrative visit scenario set in Gabon. It is a fictional scenario used to explain the approach — it does not describe, name, or imply any actual client, mandate, or government outcome.',
                'date' => '2026-08-05',
                'speaker' => 'Tony Smith, Founder & Managing Partner',
                'status' => 'published',
            ],
            [
                'type' => 'podcast',
                'title' => 'The Long Game: Economic Diplomacy in Practice',
                'description' => 'A conversation on how economic diplomacy is actually practiced day to day, away from the headlines and the signing ceremonies.',
                'date' => '2026-07-01',
                'speaker' => 'Naledi Okonjo-Reyes, Partner, Government & Political Affairs',
                'status' => 'published',
            ],
            [
                'type' => 'podcast',
                'title' => 'Reading the Room: Political Risk in Real Time',
                'description' => 'How our intelligence practice separates genuine signal from noise when a situation is still moving.',
                'date' => '2026-05-14',
                'speaker' => 'Marcus Thane, Partner, Strategic Intelligence & Analysis',
                'status' => 'published',
            ],
            [
                'type' => 'podcast',
                'title' => 'Crisis, Quietly Managed',
                'description' => 'On the discipline of managing a crisis so that, done well, most people never hear about it at all.',
                'date' => '2025-10-22',
                'speaker' => 'Elias Farrow, Partner, Crisis & Special Situations',
                'status' => 'archived',
            ],
            [
                'type' => 'podcast',
                'title' => 'What Comes After the Handshake',
                'description' => 'A forthcoming episode on what institutional investors should ask for once a deal in principle is reached, not before.',
                'date' => '2026-11-18',
                'speaker' => 'Yumi Castellanos, Partner, Investment & Capital Strategy',
                'status' => 'upcoming',
            ],

            // Underground Magazine
            [
                'type' => 'magazine',
                'title' => 'Underground Magazine — Issue 01: The Architecture of Access',
                'description' => 'Our inaugural issue, on how access to decision-makers is actually built, and why it is slower and quieter than most assume.',
                'date' => '2026-02-02',
                'speaker' => null,
                'status' => 'published',
            ],
            [
                'type' => 'magazine',
                'title' => 'Underground Magazine — Issue 02: Capital Without Borders',
                'description' => 'An issue on cross-border capital strategy, and the political context every serious allocator now has to price in.',
                'date' => '2026-05-04',
                'speaker' => null,
                'status' => 'published',
            ],
            [
                'type' => 'magazine',
                'title' => 'Underground Magazine — Issue 03: The Diplomacy of Discretion',
                'description' => 'An issue on discreet advisory practice across government affairs, security, and economic diplomacy, with a continued focus on Africa.',
                'date' => '2026-08-10',
                'speaker' => null,
                'status' => 'published',
            ],
            [
                'type' => 'magazine',
                'title' => 'Underground Magazine — Issue 04: Transition States',
                'description' => 'Our forthcoming issue on institutions in transition, from governments between mandates to companies between owners.',
                'date' => '2026-12-01',
                'speaker' => null,
                'status' => 'upcoming',
            ],
        ];

        $positions = [];

        foreach ($items as $item) {
            $positions[$item['type']] = ($positions[$item['type']] ?? 0) + 1;

            MediaItemRecord::query()->updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'date' => Carbon::parse($item['date'])->toDateString(),
                    'speaker' => $item['speaker'],
                    'status' => $item['status'],
                    'position' => $positions[$item['type']],
                ],
            );
        }
    }
}
