<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

/**
 * Invitation-only forums the firm convenes or hosts. Status (past vs
 * upcoming) is derived from the current date rather than hardcoded, so
 * the page stays correct as time passes without a code change.
 */
final class EventController extends Controller
{
    public function index(): View
    {
        $events = collect([
            [
                'name' => 'Underground Winter Roundtable',
                'date' => Carbon::parse('2026-02-12'),
                'location' => 'Geneva, Switzerland',
                'description' => 'A closed-door session for sovereign principals on the year ahead in capital flows and political risk.',
            ],
            [
                'name' => 'Strategic Capital Forum',
                'date' => Carbon::parse('2026-05-19'),
                'location' => 'Singapore',
                'description' => 'A closed forum for institutional allocators and sovereign funds comparing notes on cross-border capital strategy.',
            ],
            [
                'name' => 'Infrastructure & Transition Summit',
                'date' => Carbon::parse('2026-07-08'),
                'location' => 'London, United Kingdom',
                'description' => 'A working summit for operators, lenders, and regulators aligning on financing the next decade of infrastructure and energy transition.',
            ],
            [
                'name' => 'Underground Autumn Briefing',
                'date' => Carbon::parse('2026-10-21'),
                'location' => 'Washington, D.C., United States',
                'description' => 'An invitation-only briefing on the political and regulatory currents shaping the coming year.',
            ],
            [
                'name' => 'Global Security & Defense Dialogue',
                'date' => Carbon::parse('2026-12-03'),
                'location' => 'Abu Dhabi, United Arab Emirates',
                'description' => 'A private dialogue between defense ministries and industry principals on procurement and strategic posture.',
            ],
        ])->map(static function (array $event): array {
            $event['is_past'] = $event['date']->isPast();

            return $event;
        });

        return view('events.index', [
            'events' => $events,
        ]);
    }
}
