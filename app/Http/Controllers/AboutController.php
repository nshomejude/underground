<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * The firm's story, mission, and approach. Static, curated copy —
 * there is no "About" aggregate in the domain, so this page owns its
 * own content rather than borrowing the landing page's Narrative.
 */
final class AboutController extends Controller
{
    public function index(): View
    {
        return view('about.index', [
            'principles' => [
                [
                    'icon' => 'shield-check',
                    'title' => 'Discretion First',
                    'body' => 'Every engagement is compartmentalised by default. Clients decide what is disclosed, to whom, and when — we simply make sure it never happens by accident.',
                ],
                [
                    'icon' => 'target',
                    'title' => 'Outcomes, Not Optics',
                    'body' => 'We are retained to move a specific number, ruling, relationship, or outcome — not to produce decks. Every mandate is measured against the result it was built to deliver.',
                ],
                [
                    'icon' => 'globe',
                    'title' => 'Global, Not Generic',
                    'body' => 'Our partners live in the markets they advise on. A mandate in one region is staffed by people who have already spent years building the relationships it depends on.',
                ],
                [
                    'icon' => 'handshake',
                    'title' => 'Principals, Not Vendors',
                    'body' => 'We take on a small number of mandates at any time and sit alongside the people making the decision, not downstream of it, filing reports no one reads.',
                ],
            ],
        ]);
    }
}
