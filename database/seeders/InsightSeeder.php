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
                    'body' => $this->body($insight['slug']),
                    'published_at' => now()->modify($insight['published_at_offset']),
                ],
            );
        }
    }

    private function body(string $slug): string
    {
        return match ($slug) {
            'discretion-as-strategy' => <<<'BODY'
            Every strategic advisory firm claims discretion. Almost none of them practice it as a discipline, and
            fewer still understand it as a source of leverage rather than a cost of doing business. The distinction
            matters more than it looks. A firm that is merely quiet has simply chosen not to advertise; a firm that
            is discreet by design has built discretion into how a mandate is staffed, how information moves inside
            it, and how it ends — and that difference shows up directly in what the firm can be trusted to do.

            The visible advisory industry — the firms whose names appear in the press alongside the deals they
            worked on — competes on a narrow set of signals: league tables, headcount, the size of the last
            transaction. None of that predicts whether a principal can be trusted with something that cannot survive
            being known before it is finished. Government affairs mandates, contested successions, pre-announcement
            regulatory positioning, crisis response before the crisis is public: the mandates with the highest stakes
            are, almost by definition, the ones that cannot be won by being visible in the first place.

            This is why the quietest firms tend to hold the longest client relationships in the industry. Discretion
            compounds. A principal who has watched a firm carry one sensitive mandate without a leak has a reason to
            bring the next one, and the one after that, without re-litigating the question of trust each time. Firms
            that compete on visibility have to win each mandate on its own terms; firms that compete on discretion
            inherit the next mandate from the last one.

            None of this means discretion is passive. Compartmentalisation has to be designed, not assumed: who
            actually needs to know a client's identity to do their part of the work, what travels on a dedicated
            channel versus a shared one, which relationships get activated only when a mandate requires them and
            wound down the moment it closes. Treated this way, discretion is closer to operational security than to
            manners — a set of decisions made deliberately, mandate by mandate, rather than a reputation a firm hopes
            it has earned.

            The firms that get this right rarely say so publicly, for the obvious reason. But the pattern is
            visible enough to anyone who has worked on both sides of the table: the advisors principals keep
            returning to are, overwhelmingly, the ones whose other clients they have never heard of.
            BODY,
            'infrastructure-as-influence' => <<<'BODY'
            For most of the postwar era, a port, a rail corridor, or a transmission grid was understood primarily as
            a capital asset: something to be financed, built, operated, and depreciated on a schedule. That framing
            still holds at the level of the balance sheet. It no longer holds at the level of strategy. States and
            the sponsors who back them increasingly treat physical infrastructure as an instrument of influence in
            its own right — a way to be present, indispensable, and difficult to displace inside another country's
            economy long after the construction crews have gone home.

            The mechanism is straightforward once named. A financing package for a port concession or a transmission
            interconnector rarely stops at the asset itself; it comes attached to standards, suppliers, maintenance
            relationships, and financing terms that outlast the build by decades. Whoever sets those terms shapes
            the host country's dependencies for a generation, largely without needing a formal political
            relationship to do it. Infrastructure finance has become one of the most durable, least visible forms of
            strategic positioning available to a state or sovereign sponsor — considerably more durable, in most
            cases, than the diplomatic relationship that opened the door to it.

            This has changed what a competent bid actually needs to contain. A resource-rich state weighing rival
            financing packages for the same corridor is no longer only comparing interest rates and construction
            timelines; it is weighing which sponsor's terms leave it with more room to manoeuvre in a decade, and
            which relationships come bundled in alongside the capital. Sponsors who still bid on price and schedule
            alone are, increasingly, bidding against rivals who are pricing in influence and losing the mandates that
            matter most as a result.

            It has also raised the cost of getting a single project wrong. A concession frozen by a change in
            regulatory posture, a transition-financing package that stalls because the state and the lender never
            actually aligned on what each side wanted from the relationship beyond the capital — these are not
            unusual outcomes anymore, because the stakes on both sides have quietly grown larger than the underlying
            contract implies. Clearing a stalled infrastructure mandate today is as much a matter of realigning who
            trusts whom as it is of renegotiating terms.

            The practical implication for anyone financing, operating, or regulating this kind of asset is that the
            commercial and the strategic questions have stopped being separable. Treating a corridor, a grid, or a
            port purely as a project finance exercise is no longer a simplification — it is a way to miss most of
            what is actually being decided.
            BODY,
            'government-affairs-multipolar-world' => <<<'BODY'
            A government affairs practice built for a world with two or three centers of gravity does not transfer
            cleanly to a world with a dozen. That is the practical problem underneath the familiar observation that
            power has become more diffuse: it is not merely that more capitals matter now than mattered a generation
            ago, it is that the map of who actually decides something has stopped being reliably legible from the
            org chart alone.

            The old playbook assumed a relatively small, relatively stable set of relationships could cover most of
            what a principal needed: a handful of capitals, a handful of institutions within each, and enough tenure
            in those relationships to know who really moved a decision versus who merely sat in the room where it
            was announced. That assumption is failing in more places than it is holding. Decisions that once
            resolved cleanly inside a single ministry now move through coalitions, regional blocs, and informal
            networks of state and non-state actors that do not appear on any published organisational chart —
            and the actor who holds formal authority over a decision is, more often than a decade ago, not the actor
            who actually determines its outcome.

            This changes what "coverage" has to mean for a serious government affairs practice. Depth in a small
            number of capitals is no longer sufficient on its own; it has to be paired with genuine range across a
            wider set of them, and with people who have spent real time inside each rather than a single point of
            contact maintained from a distance. A firm that can go deep in three capitals but has no real presence
            in the ten others where a multipolar decision might actually be shaped is, in practice, offering a
            narrower service than its client believes it is buying.

            Speed has become as important as reach. In a world with more relevant capitals, the sequencing of who
            hears what, and when, has more room to go wrong — and more actors are positioned to exploit a slow read
            of where a decision is really headed. The advantage increasingly belongs to whoever notices the shift in
            momentum first, not to whoever has the most senior relationship on paper.

            None of this makes the discipline more diffuse; it makes it more demanding. Effective government affairs
            in a multipolar environment still comes down to relationships and judgment — there are simply more
            relationships that matter, and less time to work out which ones do, than the previous generation of
            practitioners were trained for.
            BODY,
            'geopolitical-realignment-capital-flows' => <<<'BODY'
            For most of the post-Cold War era, capital had one dominant compass: yield, adjusted for risk. Money
            moved toward the highest risk-adjusted return with a consistency that made most of institutional finance
            legible from that single variable alone. That compass still works, but it no longer works alone.
            Increasingly, capital is also following strategic alignment — moving toward jurisdictions, partners, and
            supply chains that a sponsor's home government considers dependable for reasons that have little to do
            with the return on the underlying asset.

            The clearest evidence is in the deals that no longer close on economics alone. Cross-border
            infrastructure financing, critical-minerals supply agreements, and strategic-sector investment are
            increasingly structured around questions that a decade ago would have been handled as an afterthought
            by a compliance team: whose capital this is, what it is aligned with, and what happens to the
            relationship if the geopolitical weather changes mid-mandate. A transaction that clears every financial
            hurdle can still stall for months over exactly these questions — and, with increasing frequency, does.

            This has real consequences for how durable investment actually gets structured. Allocators who continue
            to treat strategic alignment as a tail risk to be noted in a memo, rather than a variable to be
            underwritten from the outset, are finding their positions repriced or unwound by events that were
            visible well before they happened. The allocators managing this well are the ones treating alignment as
            a first-order input alongside yield and duration — not a replacement for financial discipline, but a
            second axis that has to be satisfied at the same time.

            It also changes who needs to be in the room when a cross-border deal is structured. Capital that follows
            alignment as well as yield needs counsel that understands both axes simultaneously: the commercial terms
            that make a deal work, and the state-level relationships and sensitivities that determine whether it is
            allowed to keep working once it closes. Treating those as two separate workstreams, run by two separate
            teams that compare notes occasionally, is a structure built for the world that is ending rather than
            the one that has arrived.

            The firms and allocators adjusting fastest are not the ones abandoning yield discipline — they are the
            ones who have stopped treating strategic alignment as background noise and started treating it as
            structure: a variable that shapes which deals get done, on what terms, and how long they last once they
            are.
            BODY,
            default => throw new \InvalidArgumentException("No article body defined for insight slug [{$slug}]."),
        };
    }
}
