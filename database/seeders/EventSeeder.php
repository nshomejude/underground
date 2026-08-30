<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\EventRecord;

/**
 * Seeds the forums shown on /events, migrated from the previously
 * hardcoded EventController array.
 */
final class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'name' => 'Underground Winter Roundtable',
                'date' => '2026-02-12',
                'location' => 'Geneva, Switzerland',
                'description' => 'A closed-door session for sovereign principals on the year ahead in capital flows and political risk.',
            ],
            [
                'name' => 'Strategic Capital Forum',
                'date' => '2026-05-19',
                'location' => 'Singapore',
                'description' => 'A closed forum for institutional allocators and sovereign funds comparing notes on cross-border capital strategy.',
            ],
            [
                'name' => 'Infrastructure & Transition Summit',
                'date' => '2026-07-08',
                'location' => 'London, United Kingdom',
                'description' => 'A working summit for operators, lenders, and regulators aligning on financing the next decade of infrastructure and energy transition.',
            ],
            [
                'name' => 'Underground Autumn Briefing',
                'date' => '2026-10-21',
                'location' => 'Washington, D.C., United States',
                'description' => 'An invitation-only briefing on the political and regulatory currents shaping the coming year.',
            ],
            [
                'name' => 'Global Security & Defense Dialogue',
                'date' => '2026-12-03',
                'location' => 'Abu Dhabi, United Arab Emirates',
                'description' => 'A private dialogue between defense ministries and industry principals on procurement and strategic posture.',
            ],
        ];

        foreach ($events as $position => $event) {
            EventRecord::query()->updateOrCreate(
                ['slug' => Str::slug($event['name'])],
                [
                    'name' => $event['name'],
                    'date' => Carbon::parse($event['date'])->toDateString(),
                    'location' => $event['location'],
                    'description' => $event['description'],
                    'position' => $position + 1,
                ],
            );
        }
    }
}
