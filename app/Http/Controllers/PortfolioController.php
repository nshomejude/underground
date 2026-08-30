<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Selected past engagements, anonymised. Every entry is tagged with one
 * of the six sectors the firm operates in — no client, government, or
 * company is ever named, consistent with the confidentiality every
 * mandate is run under.
 */
final class PortfolioController extends Controller
{
    public function index(): View
    {
        return view('portfolio.index', [
            'engagements' => [
                [
                    'sector' => 'Government & Public Sector',
                    'title' => 'Repositioning a Ministry Ahead of a Contested Election Cycle',
                    'summary' => 'Advised a national ministry through eighteen months of coalition realignment, rebuilding its standing with three separate governing blocs without a single public misstep.',
                    'outcome' => 'Policy mandate retained across a full change of government.',
                ],
                [
                    'sector' => 'Energy & Natural Resources',
                    'title' => 'Structuring a Cross-Border Transition Financing Deal',
                    'summary' => 'Aligned a resource-rich state, a multilateral lender, and a private consortium on the terms of a transition-financing package that had stalled for two years.',
                    'outcome' => 'Financing closed within seven months of engagement.',
                ],
                [
                    'sector' => 'Infrastructure & Transportation',
                    'title' => 'Clearing a Stalled Port Concession',
                    'summary' => 'Rebuilt trust between a port authority and a foreign operator whose concession had been frozen by a change in regulatory posture, restoring the commercial timeline.',
                    'outcome' => 'Concession re-signed with expanded scope.',
                ],
                [
                    'sector' => 'Defense & Security',
                    'title' => 'Managing a Sensitive Bilateral Procurement Dialogue',
                    'summary' => 'Provided discreet counsel to a defense ministry navigating a procurement decision under intense diplomatic and domestic scrutiny.',
                    'outcome' => 'Agreement reached without the process becoming public.',
                ],
                [
                    'sector' => 'Technology & Innovation',
                    'title' => 'Positioning a Sovereign Fund Ahead of a Regulatory Shift',
                    'summary' => 'Prepared a sovereign technology fund\'s portfolio companies for an incoming data-governance regime months before it was formally announced.',
                    'outcome' => 'Zero portfolio companies caught out by the new regime.',
                ],
                [
                    'sector' => 'Finance & Investments',
                    'title' => 'De-Risking a Contested Institutional Merger',
                    'summary' => 'Advised the board of an institutional allocator through a merger opposed by a vocal minority of stakeholders, aligning the narrative with the underlying economics.',
                    'outcome' => 'Merger approved with unanimous board support.',
                ],
            ],
        ]);
    }
}
