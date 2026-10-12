<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\CapabilityRecord;

/**
 * Seeds the seven named expertise practices Underground sells, from the
 * content brief. The first three are featured on the landing page.
 *
 * confirmed_lead and authorized_reference are left null for every practice:
 * nothing elsewhere in this codebase's team or portfolio data is confirmed
 * as belonging to one of these specific, newly named practices, so the
 * detail page omits those sections rather than guessing at a match.
 */
final class CapabilitySeeder extends Seeder
{
    public function run(): void
    {
        $capabilities = [
            [
                'slug' => 'government-affairs-institutional-relations',
                'title' => 'Government Affairs & Institutional Relations',
                'summary' => 'Direct, lawful access to the officials and institutions shaping policy, regulation and public investment — built on relationships that are earned, not bought.',
                'icon' => 'landmark',
                'position' => 1,
                'is_featured' => true,
                'problem_statement' => 'Governments move on their own timeline, through channels principals rarely see from the outside. Clients need a lawful, disciplined way to reach the right institution, at the right level, without guesswork or delay.',
                'audiences' => [
                    'Heads of state, ministers and senior officials seeking a disciplined channel to outside counsel',
                    'Corporations and investors whose plans depend on a licence, approval or regulatory position',
                    'Multilateral and regional bodies coordinating with national governments',
                    'Civil-society and industry coalitions seeking a credible institutional voice',
                ],
                'method' => "We map the officials, institutions and informal influence around a decision before a single meeting is arranged, then sequence engagement so each conversation builds on the last — always within the law and the client's own values.",
                'deliverables' => [
                    'A stakeholder and authority map specific to the decision at hand',
                    'A sequenced engagement plan naming who is approached, in what order, and why',
                    'Briefing materials prepared for each principal meeting',
                    'Ongoing monitoring of the political developments that could change the terms of engagement',
                ],
                'confirmed_lead' => null,
                'authorized_reference' => null,
                'next_action' => 'Start a confidential inquiry',
            ],
            [
                'slug' => 'economic-submarine-diplomacy',
                'title' => 'Economic Diplomacy & Submarine Diplomacy',
                'summary' => 'A discreet, prepared channel for sensitive economic dialogue between principals — convened quietly, documented carefully, and never presented as more than a confidential conversation.',
                'icon' => 'ship-wheel',
                'position' => 2,
                'is_featured' => true,
                'problem_statement' => 'Some economic conversations cannot begin in public. A proposal or a sensitive negotiation needs a quiet, controlled setting before either side is ready to be seen talking, and it needs the same rigor as any formal negotiation even though it happens out of view.',
                'audiences' => [
                    'Governments exploring an economic relationship before either side is ready for a public announcement',
                    'Investors and sponsors who need a confidential first conversation with an authority before committing resources to formal talks',
                    'Principals who require absolute discretion while a sensitive economic question is explored',
                    "Advisors coordinating a quiet channel alongside a client's formal diplomatic or commercial process",
                ],
                'method' => '"Submarine Diplomacy" is our term for a quiet, prepared channel of economic dialogue: a confidential conversation convened between principals, built on careful advance preparation rather than improvisation. We confirm mandate and intent on both sides before a meeting is set, prepare each principal with the same rigor as a public negotiation, and keep the channel closed unless and until the parties themselves choose to make it public. We do not promise a government outcome, and we do not operate outside the law or ethics rules of any jurisdiction involved.',
                'deliverables' => [
                    'A confirmed channel between principals, opened only once mandate and intent are established on both sides',
                    'A prepared agenda and briefing for each confidential conversation',
                    "A documented record of what was discussed and agreed, for the client's own file",
                    'A clear recommendation on whether and when to move the conversation into a formal, public process',
                ],
                'confirmed_lead' => null,
                'authorized_reference' => null,
                'next_action' => 'Start a confidential inquiry',
            ],
            [
                'slug' => 'strategic-intelligence-research',
                'title' => 'Strategic Intelligence & Research',
                'summary' => 'Grounded, real-time reads on the political, economic and security dynamics shaping a decision, built from lawful sources and stated with honest confidence.',
                'icon' => 'radar',
                'position' => 3,
                'is_featured' => true,
                'problem_statement' => 'Decisions get made on incomplete information more often than principals realize. Clients need a clear, honestly caveated picture of the political, economic and security environment around a decision, not a confident-sounding guess.',
                'audiences' => [
                    'Boards and executives weighing a market-entry or investment decision',
                    'Investors assessing the risk around a counterparty, country or transaction',
                    'Government and institutional clients tracking a fast-moving political situation',
                    'Advisors who need an independent, rigorously sourced second opinion',
                ],
                'method' => 'We work only from lawful, ethical sources — open research, expert networks and field relationships — and we test competing explanations before we commit to one. Every assessment states plainly how confident we are and what could change the picture.',
                'deliverables' => [
                    'A structured assessment of the political, economic or security conditions around a decision',
                    'A named set of assumptions and an explicit confidence level for the judgment reached',
                    'Rapid-response briefings when a fast-moving situation changes the picture',
                    'Ongoing horizon-scanning on the issues most likely to affect the client',
                ],
                'confirmed_lead' => null,
                'authorized_reference' => null,
                'next_action' => 'Start a confidential inquiry',
            ],
            [
                'slug' => 'investment-business-partnerships',
                'title' => 'Investment & Business Partnerships',
                'summary' => 'Structuring capital, investment strategy and commercial partnerships alongside the political and regulatory reality on the ground.',
                'icon' => 'handshake',
                'position' => 4,
                'is_featured' => false,
                'problem_statement' => 'An investment thesis or partnership that looks sound on paper can still fail against the political and regulatory reality it has to operate in. Clients need that reality tested, and the right partners and capital identified, before commitments are made.',
                'audiences' => [
                    'Investors and sponsors testing a market-entry or capital-deployment thesis',
                    'Companies seeking a qualified commercial partner, distributor or joint-venture candidate in a new market',
                    'Development finance institutions and strategic capital allocators seeking aligned co-investors',
                    'Principals navigating the approvals and sequencing a transaction depends on',
                ],
                'method' => 'We test an investment or partnership thesis against the political and regulatory reality of the market it will operate in, then make the introductions — to capital, to counterparties, to institutions — the thesis actually depends on.',
                'deliverables' => [
                    'A reality-tested view of an investment or partnership thesis',
                    'Qualified introductions to aligned capital, partners or counterparties',
                    'A mapped view of the approvals and sequencing a transaction will need to clear',
                    "Ongoing support through negotiation and into the partnership's first year",
                ],
                'confirmed_lead' => null,
                'authorized_reference' => null,
                'next_action' => 'Start a confidential inquiry',
            ],
            [
                'slug' => 'strategic-communications-reputation',
                'title' => 'Strategic Communications & Reputation',
                'summary' => 'Shaping how leaders, institutions and moments are understood by the audiences that decide their fate.',
                'icon' => 'megaphone',
                'position' => 5,
                'is_featured' => false,
                'problem_statement' => 'Reputation is decided by audiences a client rarely speaks to directly, and it is usually tested at the worst possible moment. Clients need a consistent narrative in calm times and a composed, prepared response when that narrative is challenged.',
                'audiences' => [
                    'Executives and principals whose public standing shapes a deal, mandate or career',
                    'Institutions preparing for scrutiny ahead of a transaction, appointment or announcement',
                    'Organizations facing an active reputational or narrative challenge',
                    'Leaders who need to be camera- and room-ready under pressure',
                ],
                'method' => 'We start by understanding how a client is actually perceived, by whom, and why, then build a narrative, a set of messengers and a response plan that holds up under real scrutiny, not just in a rehearsal room.',
                'deliverables' => [
                    'An honest audit of current perception across the audiences that matter',
                    'A defined narrative, message set and spokesperson plan',
                    'Media and stakeholder preparation for principals',
                    'A standing response plan for when the narrative is tested',
                ],
                'confirmed_lead' => null,
                'authorized_reference' => null,
                'next_action' => 'Start a confidential inquiry',
            ],
            [
                'slug' => 'public-policy-special-task-forces',
                'title' => 'Public Policy & Special Task Forces',
                'summary' => 'Structured policy counsel and a senior task force that can be mobilized within hours when a situation will not wait.',
                'icon' => 'flag',
                'position' => 6,
                'is_featured' => false,
                'problem_statement' => "Some policy questions and crises move faster than an organization's normal advisory relationships can respond. Clients need both long-horizon policy counsel and the ability to stand up a senior, focused team within hours when a situation demands it.",
                'audiences' => [
                    'Organizations navigating pending legislation, regulation or a public-policy question',
                    'Leaders facing a sudden political, regulatory or reputational situation that cannot wait',
                    'Institutions seeking a coordinated, compliant position on a shared policy issue',
                    'Principals who need a small, senior team mobilized on short notice',
                ],
                'method' => 'For policy work, we analyze the issue, the decision-makers and the timeline, then build a compliant, documented engagement plan. For urgent situations, we assemble a small, senior task force quickly, stabilize the immediate position, and stay until the situation is resolved.',
                'deliverables' => [
                    'A policy position and engagement plan compliant with local disclosure and ethics rules',
                    'A named senior task force, mobilized on short notice when a situation demands it',
                    'A rapid situation assessment with clear options for leadership',
                    'A closing review and resilience recommendations once the matter is resolved',
                ],
                'confirmed_lead' => null,
                'authorized_reference' => null,
                'next_action' => 'Start a confidential inquiry',
            ],
            [
                'slug' => 'infrastructure-industrial-development-advisory',
                'title' => 'Infrastructure & Industrial Development Advisory',
                'summary' => 'Advising governments, sponsors and financiers on the alignment of public purpose, private capital and community acceptance that major projects depend on.',
                'icon' => 'building-2',
                'position' => 7,
                'is_featured' => false,
                'problem_statement' => 'Major infrastructure and industrial projects succeed only when public purpose, private capital and community acceptance move together — an alignment that is slow, political and easy to lose. Clients need that alignment built deliberately, not assumed.',
                'audiences' => [
                    'Governments and agencies preparing to partner with the private sector on infrastructure or industrial projects',
                    'Sponsors, operators and financiers seeking to participate in ports, corridors, energy systems or industrial assets',
                    "Development finance institutions assessing a project's readiness",
                    'Communities and local stakeholders whose acceptance a project depends on',
                ],
                'method' => "We test a project's political, social and financial foundations before it goes to market, align public, private and community interests around a shared position, and stay engaged through procurement and delivery.",
                'deliverables' => [
                    "An assessment of a project's political, social and financial readiness",
                    'An alignment plan connecting public authorities, private capital and affected communities',
                    'Introductions to financiers, sponsors and experienced operators',
                    'Ongoing support through procurement, financial close and delivery',
                ],
                'confirmed_lead' => null,
                'authorized_reference' => null,
                'next_action' => 'Start a confidential inquiry',
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
                    'problem_statement' => $capability['problem_statement'],
                    'audiences' => $capability['audiences'],
                    'method' => $capability['method'],
                    'deliverables' => $capability['deliverables'],
                    'confirmed_lead' => $capability['confirmed_lead'],
                    'authorized_reference' => $capability['authorized_reference'],
                    'next_action' => $capability['next_action'],
                ],
            );
        }

        // This seeder replaces the catalogue wholesale: any capability
        // seeded under the old eight-discipline list (or anything else no
        // longer in the seven above) is retired rather than left stranded.
        CapabilityRecord::query()
            ->whereNotIn('slug', array_column($capabilities, 'slug'))
            ->delete();
    }
}
