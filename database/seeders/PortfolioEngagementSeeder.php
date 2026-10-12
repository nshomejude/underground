<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Infrastructure\Persistence\Eloquent\Models\PortfolioEngagementRecord;

/**
 * Seeds the engagements shown on /portfolio and, with the extra
 * case-study fields below, on /team/case-studies — migrated from the
 * previously hardcoded PortfolioController array.
 *
 * `summary` already functions as each case study's context and
 * `outcome` as its demonstrable result (see PortfolioEngagement's class
 * doc). realRole and deliverables here are a generic restructuring of
 * what each summary already describes, not new facts; `period` is left
 * null wherever the source summary never stated a concrete duration,
 * rather than inventing one (two of the six do state one — eighteen
 * months and seven months — and those are reused as-is). None of these
 * six anonymised engagements has an actual publication/attribution
 * agreement on file, so publicationAgreementNote says that plainly
 * instead of implying consent that was never given.
 */
final class PortfolioEngagementSeeder extends Seeder
{
    private const NO_PUBLICATION_AGREEMENT = 'No publication agreement on file. Presented in anonymised, generalised form consistent with firm confidentiality practice — no client, government or institution is named.';

    public function run(): void
    {
        $engagements = [
            [
                'sector' => 'Government & Public Sector',
                'title' => 'Repositioning a Ministry Ahead of a Contested Election Cycle',
                'summary' => 'Advised a national ministry through eighteen months of coalition realignment, rebuilding its standing with three separate governing blocs without a single public misstep.',
                'outcome' => 'Policy mandate retained across a full change of government.',
                'real_role' => 'Retained strategic and government-relations advisor to the ministry.',
                'deliverables' => [
                    'Coalition realignment strategy',
                    'Government relations continuity plan',
                    'Policy mandate defense through the governing transition',
                ],
                'period' => '18 months',
            ],
            [
                'sector' => 'Energy & Natural Resources',
                'title' => 'Structuring a Cross-Border Transition Financing Deal',
                'summary' => 'Aligned a resource-rich state, a multilateral lender, and a private consortium on the terms of a transition-financing package that had stalled for two years.',
                'outcome' => 'Financing closed within seven months of engagement.',
                'real_role' => 'Transaction advisor coordinating sovereign, multilateral and private-sector counterparties.',
                'deliverables' => [
                    'Multi-party negotiation framework',
                    'Financing term sheet alignment across three counterparties',
                    'Closing timeline and sequencing plan',
                ],
                'period' => '7 months',
            ],
            [
                'sector' => 'Infrastructure & Transportation',
                'title' => 'Clearing a Stalled Port Concession',
                'summary' => 'Rebuilt trust between a port authority and a foreign operator whose concession had been frozen by a change in regulatory posture, restoring the commercial timeline.',
                'outcome' => 'Concession re-signed with expanded scope.',
                'real_role' => 'Advisor to the operator on regulatory and government relations strategy.',
                'deliverables' => [
                    'Regulatory posture assessment',
                    'Port authority relationship restoration plan',
                    'Revised concession terms and expanded-scope proposal',
                ],
                'period' => null,
            ],
            [
                'sector' => 'Defense & Security',
                'title' => 'Managing a Sensitive Bilateral Procurement Dialogue',
                'summary' => 'Provided discreet counsel to a defense ministry navigating a procurement decision under intense diplomatic and domestic scrutiny.',
                'outcome' => 'Agreement reached without the process becoming public.',
                'real_role' => 'Discreet counsel to the ministry throughout the procurement dialogue.',
                'deliverables' => [
                    'Diplomatic sensitivity and risk assessment',
                    'Confidential stakeholder engagement plan',
                ],
                'period' => null,
            ],
            [
                'sector' => 'Technology & Innovation',
                'title' => 'Positioning a Sovereign Fund Ahead of a Regulatory Shift',
                'summary' => 'Prepared a sovereign technology fund\'s portfolio companies for an incoming data-governance regime months before it was formally announced.',
                'outcome' => 'Zero portfolio companies caught out by the new regime.',
                'real_role' => 'Regulatory-readiness advisor to the fund and its portfolio companies.',
                'deliverables' => [
                    'Regulatory horizon-scanning briefing',
                    'Portfolio-wide data-governance readiness review',
                ],
                'period' => null,
            ],
            [
                'sector' => 'Finance & Investments',
                'title' => 'De-Risking a Contested Institutional Merger',
                'summary' => 'Advised the board of an institutional allocator through a merger opposed by a vocal minority of stakeholders, aligning the narrative with the underlying economics.',
                'outcome' => 'Merger approved with unanimous board support.',
                'real_role' => 'Advisor to the board on stakeholder alignment and narrative strategy.',
                'deliverables' => [
                    'Stakeholder opposition analysis',
                    'Board narrative and alignment strategy',
                ],
                'period' => null,
            ],
        ];

        foreach ($engagements as $position => $engagement) {
            PortfolioEngagementRecord::query()->updateOrCreate(
                ['slug' => Str::slug($engagement['title'])],
                [
                    'sector' => $engagement['sector'],
                    'title' => $engagement['title'],
                    'summary' => $engagement['summary'],
                    'outcome' => $engagement['outcome'],
                    'real_role' => $engagement['real_role'],
                    'deliverables' => $engagement['deliverables'],
                    'period' => $engagement['period'],
                    'publication_agreement_note' => self::NO_PUBLICATION_AGREEMENT,
                    'position' => $position + 1,
                ],
            );
        }
    }
}
