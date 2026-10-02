<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MembershipPlan;
use App\Models\PlanChangeRequest;
use App\Models\User;
use App\Notifications\PlanRequestDecidedNotification;
use App\Notifications\PlanRequestReceivedNotification;
use App\Notifications\PlanRequestStaffNotification;
use Application\Membership\Queries\ListMembershipTiers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Infrastructure\Persistence\Eloquent\Models\MembershipApplicationRecord;
use Infrastructure\Persistence\Eloquent\Models\MembershipTierRecord;

/**
 * Membership plans, relative upgrade/downgrade labels and plan change requests.
 * No payment processing: a request is a note to staff, who handle the change.
 */
final class PlanService
{
    public const FORUM_LABELS = [
        'none' => 'Not included',
        'selected' => 'Selected forums by invitation',
        'all' => 'Standing access',
    ];

    public const RESPONSE_LABELS = [
        'standard' => 'Standard',
        'priority' => 'Priority',
        'dedicated' => 'Priority, named partner',
    ];

    public const VOTE_LABELS = [
        1 => 'Affiliate-level motions',
        2 => 'Up to Principal-level motions',
        3 => 'Every motion',
    ];

    public function __construct(
        private readonly MemberAccess $access,
        private readonly ListMembershipTiers $tiers,
    ) {}

    /** @return Collection<int, MembershipPlan> */
    public function activePlans(): Collection
    {
        return MembershipPlan::query()
            ->where('is_active', true)
            ->orderBy('position')
            ->orderBy('id')
            ->get();
    }

    /** The plan matching the member's approved tier, or null for non-members. */
    public function currentPlan(User $user): ?MembershipPlan
    {
        $slug = $this->access->tierSlug($user);

        if ($slug === null) {
            return null;
        }

        return MembershipPlan::query()
            ->where('tier_slug', $slug)
            ->orderByDesc('is_active')
            ->orderBy('position')
            ->first();
    }

    /** 'current' | 'upgrade' | 'downgrade' | 'none' (no membership) for a plan. */
    public function relation(MembershipPlan $plan, int $currentRank): string
    {
        if ($currentRank === 0) {
            return 'none';
        }

        return match (true) {
            $plan->rank() === $currentRank => 'current',
            $plan->rank() > $currentRank => 'upgrade',
            default => 'downgrade',
        };
    }

    public function pendingRequest(User $user): ?PlanChangeRequest
    {
        return PlanChangeRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('toPlan')
            ->latest('id')
            ->first();
    }

    /** Props for a sample <x-membership-card> of the given tier (illustrative data only). */
    public function sampleCard(string $tierSlug): ?array
    {
        $tier = $this->tiers->bySlug($tierSlug);

        if ($tier === null) {
            return null;
        }

        $samples = [
            'sovereign-partner' => [
                'variant' => 'organisation',
                'name' => 'Republic of Valcoria — Ministry of Finance',
                'representative' => 'Dr. Amara N. Osei',
                'representativeTitle' => 'Permanent Secretary',
                'memberId' => 'UG · 2018 · 000012',
                'issuedOn' => Carbon::parse('2024-01-15'),
                'validThrough' => Carbon::parse('2029-01-14'),
            ],
            'principal-circle' => [
                'variant' => 'individual',
                'name' => 'Isabelle Fontaine-Whitmore',
                'representative' => null,
                'representativeTitle' => null,
                'memberId' => 'UG · 2023 · 004821',
                'issuedOn' => Carbon::parse('2026-06-01'),
                'validThrough' => Carbon::parse('2031-05-31'),
            ],
            'corporate-affiliate' => [
                'variant' => 'organisation',
                'name' => 'Castellane Atlantic Holdings',
                'representative' => 'Marcus Reyes',
                'representativeTitle' => 'Chief Strategy Officer',
                'memberId' => 'UG · 2021 · 001147',
                'issuedOn' => Carbon::parse('2025-03-01'),
                'validThrough' => Carbon::parse('2030-02-28'),
            ],
        ];

        return isset($samples[$tierSlug]) ? ['tier' => $tier] + $samples[$tierSlug] : null;
    }

    /**
     * Feature comparison rows: [label, [planId => string|bool]].
     *
     * @param  Collection<int, MembershipPlan>  $plans
     * @return list<array{0: string, 1: array<int, string|bool>}>
     */
    public function matrix(Collection $plans): array
    {
        $row = static function (string $label, callable $cell) use ($plans): array {
            return [$label, $plans->mapWithKeys(fn (MembershipPlan $p) => [$p->id => $cell($p->limits ?? [])])->all()];
        };

        return [
            ['Membership card and certificate', $plans->mapWithKeys(fn (MembershipPlan $p) => [$p->id => true])->all()],
            ['Member directory listing', $plans->mapWithKeys(fn (MembershipPlan $p) => [$p->id => true])->all()],
            $row('Connection requests per month', fn (array $l) => array_key_exists('connection_requests_per_month', $l) && $l['connection_requests_per_month'] !== null
                ? (string) $l['connection_requests_per_month']
                : 'Unlimited'),
            ['Private messaging with connections', $plans->mapWithKeys(fn (MembershipPlan $p) => [$p->id => true])->all()],
            $row('Votes on', fn (array $l) => self::VOTE_LABELS[(int) ($l['vote_rank'] ?? 1)] ?? self::VOTE_LABELS[1]),
            $row('Open motions for a vote', fn (array $l) => (bool) ($l['can_create_motions'] ?? false)),
            $row('Invitation-only forums', fn (array $l) => self::FORUM_LABELS[$l['forum_access'] ?? 'none'] ?? self::FORUM_LABELS['none']),
            $row('Inquiry response', fn (array $l) => self::RESPONSE_LABELS[$l['inquiry_response'] ?? 'standard'] ?? self::RESPONSE_LABELS['standard']),
            ['Verified identity and company badges', $plans->mapWithKeys(fn (MembershipPlan $p) => [$p->id => true])->all()],
        ];
    }

    /**
     * Record an upgrade request, email the member a receipt and tell staff.
     *
     * @throws ValidationException
     */
    public function submit(User $user, MembershipPlan $target, ?string $note, ?int $amountCents = null): PlanChangeRequest
    {
        $current = $this->currentPlan($user);
        $currentRank = $this->access->tierRank($user);

        if ($currentRank === 0) {
            throw ValidationException::withMessages(['plan_id' => 'Only approved members can request a plan change.']);
        }

        if (! $target->is_active) {
            throw ValidationException::withMessages(['plan_id' => 'That plan is not available.']);
        }

        if ($target->rank() === $currentRank) {
            throw ValidationException::withMessages(['plan_id' => 'That is already your plan.']);
        }

        if ($target->rank() < $currentRank) {
            throw ValidationException::withMessages(['plan_id' => 'Downgrades are handled by our team. Please contact us.']);
        }

        if ($this->pendingRequest($user) !== null) {
            throw ValidationException::withMessages(['plan_id' => 'You already have a plan request awaiting review. Withdraw it before sending another.']);
        }

        if ($target->hasPrice()) {
            $min = (int) $target->price_cents;
            $max = $target->maxCents() ?? $min;
            $amountCents ??= $min;

            if ($amountCents < $min || $amountCents > $max) {
                throw ValidationException::withMessages(['amount' => 'The annual fee for '.$target->name.' is between '.$target->currency.' '.number_format($min / 100).' and '.number_format($max / 100).'.']);
            }
        } else {
            $amountCents = null;
        }

        $request = PlanChangeRequest::create([
            'amount_cents' => $amountCents,
            'user_id' => $user->id,
            'from_plan_id' => $current?->id,
            'to_plan_id' => $target->id,
            'status' => 'pending',
            'note' => $note !== null && trim($note) !== '' ? trim($note) : null,
        ]);

        $request->load(['toPlan', 'fromPlan', 'user']);

        $user->notify(new PlanRequestReceivedNotification($request));
        Notification::route('mail', config('mail.from.address'))->notify(new PlanRequestStaffNotification($request));

        return $request;
    }

    public function withdraw(PlanChangeRequest $request): void
    {
        if ($request->isPending()) {
            $request->update(['status' => 'withdrawn']);
        }
    }

    /**
     * The automatic upgrader: moves the member's approved application to the
     * requested tier, so card, directory standing and voting rights follow at once.
     */
    public function applyUpgrade(PlanChangeRequest $request): bool
    {
        $request->loadMissing(['user', 'toPlan']);
        $user = $request->user;
        $tierId = MembershipTierRecord::query()->where('slug', $request->toPlan?->tier_slug)->value('id');

        if ($user === null || $tierId === null) {
            return false;
        }

        $changed = MembershipApplicationRecord::query()
            ->where('email', $user->email)
            ->where('status', 'approved')
            ->update(['tier_id' => $tierId]);

        if ($changed > 0) {
            $request->update(['applied_at' => now()]);
        }

        return $changed > 0;
    }

    /** Record staff's decision, apply an approved upgrade and email the member. */
    public function decide(PlanChangeRequest $request, User $reviewer, bool $approve, ?string $responseNote): PlanChangeRequest
    {
        $request->update([
            'status' => $approve ? 'approved' : 'declined',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'response_note' => $responseNote !== null && trim($responseNote) !== '' ? trim($responseNote) : null,
        ]);

        if ($approve) {
            $this->applyUpgrade($request);
        }

        $request->load(['toPlan', 'fromPlan', 'user']);
        $request->user?->notify(new PlanRequestDecidedNotification($request));

        return $request;
    }
}
