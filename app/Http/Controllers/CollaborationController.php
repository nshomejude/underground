<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * How the firm actually works day-to-day once a mandate is live —
 * distinct from the sales-facing "Engagement Models" strip on the
 * landing page, which describes commercial shape rather than cadence.
 */
final class CollaborationController extends Controller
{
    public function index(): View
    {
        return view('collaboration.index', [
            'modes' => [
                [
                    'icon' => 'users',
                    'title' => 'Embedded Teams',
                    'body' => 'A small, named team sits inside your organisation for the duration of the mandate, reporting to the principal directly rather than through a layer of account management.',
                ],
                [
                    'icon' => 'handshake',
                    'title' => 'Joint Working Groups',
                    'body' => 'For multi-party mandates, we convene a standing working group across every side of the table, with a single shared record of decisions and next steps.',
                ],
                [
                    'icon' => 'lock',
                    'title' => 'Secure Channels',
                    'body' => 'Every mandate runs on a dedicated, encrypted channel — no shared drives, no email threads that outlive the people who need to see them.',
                ],
                [
                    'icon' => 'clock',
                    'title' => 'Engagement Cadence',
                    'body' => 'A standing weekly briefing, a same-day escalation line for anything time-critical, and a partner-level review at every material milestone.',
                ],
            ],
            'principles' => [
                'One senior partner is named and accountable for every mandate — never a rotating cast.',
                'Nothing material moves without the principal\'s sign-off; we advise, the client decides.',
                'Every working group and channel is wound down and archived the moment a mandate closes.',
            ],
        ]);
    }
}
