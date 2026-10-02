<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Motion;
use App\Models\MotionEvent;
use App\Models\User;
use App\Models\Vote;
use App\Notifications\MotionClosedNotification;
use App\Notifications\MotionOpenedNotification;
use App\Notifications\MotionReminderNotification;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Internal voting: creating, publishing, casting, tallying and closing motions.
 *
 * Anonymity: a vote row always exists (it is what stops double voting) but for
 * anonymous motions nothing outside the voter's own session ever reads which
 * member chose what: no list, no export, no audit entry.
 */
final class MotionService
{
    /** Above this many recipients, emails are sent by the scheduled `votes:close-due` pass. */
    public const INLINE_NOTIFY_LIMIT = 50;

    public function __construct(private readonly MemberAccess $access) {}

    // ---------------------------------------------------------------- permissions

    public function canView(User $user, Motion $motion): bool
    {
        if ($user->is_admin || $motion->created_by === $user->id) {
            return true;
        }

        if ($motion->isDraft()) {
            return false;
        }

        if ($motion->isCancelled() && $motion->published_at === null) {
            return false;
        }

        return $this->access->canVote($user, (int) $motion->min_tier_rank);
    }

    public function canVote(User $user, Motion $motion): bool
    {
        return $this->access->canUseNetwork($user) && $this->access->canVote($user, (int) $motion->min_tier_rank);
    }

    /** Creator may edit a draft, or a published motion nobody has voted on yet. */
    public function canEdit(User $user, Motion $motion): bool
    {
        if (! ($user->is_admin || $motion->created_by === $user->id)) {
            return false;
        }

        if ($motion->isDraft()) {
            return true;
        }

        return $motion->status === Motion::OPEN && ! $motion->votes()->exists();
    }

    public function canCancel(User $user, Motion $motion): bool
    {
        if (! in_array($motion->status, [Motion::DRAFT, Motion::OPEN], true)) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        return $motion->created_by === $user->id && ! $motion->votes()->exists();
    }

    public function canSeeVoters(User $user, Motion $motion): bool
    {
        return ! $motion->anonymous && ($user->is_admin || $motion->created_by === $user->id);
    }

    /** Results are sealed until the motion closes, unless it was set to show live. */
    public function resultsVisible(Motion $motion): bool
    {
        if ($motion->isDecided()) {
            return true;
        }

        return $motion->status === Motion::OPEN && $motion->show_results === 'live';
    }

    // ---------------------------------------------------------------- lifecycle

    /** @param  array<string, mixed>  $data  Normalised by MotionRequest::motionData(). */
    public function create(User $creator, array $data, bool $publish = false): Motion
    {
        if (! $this->access->canCreateMotion($creator)) {
            throw new MotionException('You are not permitted to open motions.');
        }

        $motion = Motion::create($this->attributes($data) + [
            'created_by' => $creator->id,
            'status' => Motion::DRAFT,
        ]);

        $this->log($motion, 'created', $creator);

        return $publish ? $this->publish($motion, $creator) : $motion;
    }

    /** @param  array<string, mixed>  $data */
    public function update(Motion $motion, User $actor, array $data): Motion
    {
        if (! $this->canEdit($actor, $motion)) {
            throw new MotionException('This motion can no longer be edited because voting has begun.');
        }

        $motion->update($this->attributes($data));
        $this->log($motion, 'edited', $actor);

        return $motion;
    }

    public function publish(Motion $motion, User $actor): Motion
    {
        if (! $motion->isDraft()) {
            throw new MotionException('Only a draft can be published.');
        }

        if (! ($actor->is_admin || $motion->created_by === $actor->id)) {
            throw new MotionException('You are not permitted to publish this motion.');
        }

        $opens = $motion->opens_at !== null && $motion->opens_at->isFuture() ? $motion->opens_at : now();
        $this->assertWindow($opens, $motion->closes_at);

        $motion->update([
            'status' => Motion::OPEN,
            'opens_at' => $opens,
            'published_at' => now(),
        ]);
        $this->log($motion, 'published', $actor, ['opens_at' => $opens->toIso8601String()]);

        if ($motion->opens_at->lte(now())) {
            $this->notifyOpened($motion, true);
        }

        return $motion;
    }

    public function cancel(Motion $motion, User $actor, ?string $reason = null): Motion
    {
        if (! $this->canCancel($actor, $motion)) {
            throw new MotionException('This motion cannot be cancelled.');
        }

        $motion->update([
            'status' => Motion::CANCELLED,
            'cancelled_at' => now(),
            'cancel_reason' => $reason,
        ]);
        $this->log($motion, 'cancelled', $actor, ['reason' => $reason]);

        return $motion;
    }

    /** Staff admins only: end voting now and record the outcome. */
    public function closeEarly(Motion $motion, User $actor, string $reason): Motion
    {
        if (! $actor->is_admin) {
            throw new MotionException('Only staff administrators can close a motion early.');
        }

        if (! $motion->isOpenNow()) {
            throw new MotionException('Only a motion that is open for voting can be closed early.');
        }

        $this->finalise($motion, $actor, ['closed_early' => true, 'reason' => $reason]);

        return $motion->refresh();
    }

    /** Casts or changes a vote. */
    public function castVote(Motion $motion, User $user, string $choice, ?string $comment = null): Vote
    {
        $vote = DB::transaction(function () use ($motion, $user, $choice, $comment): Vote {
            /** @var Motion $locked */
            $locked = Motion::query()->lockForUpdate()->findOrFail($motion->id);

            if (! $this->canVote($user, $locked)) {
                throw new MotionException('You are not eligible to vote on this motion.');
            }

            if ($locked->isScheduled() || $locked->isDraft()) {
                throw new MotionException('Voting on this motion has not opened yet.');
            }

            if ($locked->isCancelled()) {
                throw new MotionException('This motion was cancelled.');
            }

            if ($locked->status !== Motion::OPEN || $locked->closes_at === null || $locked->closes_at->lte(now())) {
                throw new MotionException('Voting on this motion has closed.');
            }

            if (! in_array($choice, $locked->choiceList(), true)) {
                throw new MotionException('That is not one of the choices on this motion.');
            }

            $comment = $locked->anonymous ? null : (($comment !== null && trim($comment) !== '') ? mb_substr(trim($comment), 0, 500) : null);
            $weight = $locked->weighted ? max(1, $this->access->tierRank($user)) : 1;

            $existing = Vote::query()->where('motion_id', $locked->id)->where('user_id', $user->id)->lockForUpdate()->first();

            if ($existing !== null) {
                if (! $locked->allow_change) {
                    throw new MotionException('You have already voted and this motion does not allow changes.');
                }

                $existing->update(['choice' => $choice, 'weight' => $weight, 'comment' => $comment]);
                $this->log($locked, 'vote_changed', $locked->anonymous ? null : $user, $locked->anonymous ? [] : ['choice' => $choice]);

                return $existing;
            }

            try {
                $vote = Vote::create([
                    'motion_id' => $locked->id,
                    'user_id' => $user->id,
                    'choice' => $choice,
                    'weight' => $weight,
                    'comment' => $comment,
                ]);
            } catch (QueryException) {
                throw new MotionException('You have already voted on this motion.');
            }

            $count = Vote::query()->where('motion_id', $locked->id)->count();
            $this->log($locked, 'vote_cast', $locked->anonymous ? null : $user, $locked->anonymous ? ['votes_total' => $count] : ['choice' => $choice, 'votes_total' => $count]);

            return $vote;
        });

        return $vote;
    }

    // ---------------------------------------------------------------- tally and closing

    /**
     * Counts, percentages and the outcome as they stand now.
     *
     * @return array<string, mixed>
     */
    public function tally(Motion $motion): array
    {
        $choices = $motion->choiceList();
        $counts = array_fill_keys($choices, 0);
        $voters = 0;

        foreach (Vote::query()->where('motion_id', $motion->id)->get(['choice', 'weight']) as $vote) {
            if (array_key_exists($vote->choice, $counts)) {
                $counts[$vote->choice] += (int) $vote->weight;
            }
            $voters++;
        }

        $total = array_sum($counts);
        $percentages = [];
        foreach ($counts as $choice => $count) {
            $percentages[$choice] = $total > 0 ? round($count / $total * 100, 1) : 0.0;
        }

        $quorum = (int) $motion->quorum;
        $threshold = (float) $motion->pass_threshold;
        $winners = [];
        $forShare = null;
        $quorumMet = $voters >= $quorum;

        if (! $quorumMet) {
            $outcome = 'no_quorum';
        } elseif ($motion->isDecision()) {
            $for = $counts['For'] ?? 0;
            $against = $counts['Against'] ?? 0;
            $decisive = $for + $against;
            $forShare = $decisive > 0 ? round($for / $decisive * 100, 1) : null;
            // Inclusive threshold, compared without float division so exact edges hold.
            $outcome = $decisive > 0 && round($for * 100, 6) >= round($threshold * $decisive, 6) ? 'passed' : 'rejected';
        } else {
            $max = $counts === [] ? 0 : max($counts);
            $winners = $max > 0 ? array_keys(array_filter($counts, fn (int $c): bool => $c === $max)) : [];
            $outcome = count($winners) === 1 ? 'decided' : 'tied';
        }

        return [
            'counts' => $counts,
            'percentages' => $percentages,
            'voters' => $voters,
            'weighted_total' => $total,
            'weighted' => (bool) $motion->weighted,
            'quorum' => $quorum,
            'quorum_met' => $quorumMet,
            'threshold' => $threshold,
            'for_share' => $forShare,
            'winners' => $winners,
            'outcome' => $outcome,
        ];
    }

    public function closeIfDue(Motion $motion): bool
    {
        if ($motion->status !== Motion::OPEN || $motion->closes_at === null || $motion->closes_at->isFuture()) {
            return false;
        }

        return $this->finalise($motion, null, []);
    }

    /** Closes every motion past its closing time. Idempotent; returns how many were closed now. */
    public function closeDue(): int
    {
        $closed = 0;

        Motion::query()
            ->where('status', Motion::OPEN)
            ->where('closes_at', '<=', now())
            ->get()
            ->each(function (Motion $motion) use (&$closed): void {
                if ($this->closeIfDue($motion)) {
                    $closed++;
                }
            });

        return $closed;
    }

    /** @param  array<string, mixed>  $extra */
    private function finalise(Motion $motion, ?User $actor, array $extra): bool
    {
        $done = DB::transaction(function () use ($motion, $actor, $extra): bool {
            /** @var Motion $locked */
            $locked = Motion::query()->lockForUpdate()->findOrFail($motion->id);

            if ($locked->status !== Motion::OPEN) {
                return false;
            }

            $tally = $this->tally($locked);
            $closedAt = now();
            $status = match ($tally['outcome']) {
                'passed' => Motion::PASSED,
                'rejected' => Motion::REJECTED,
                default => Motion::CLOSED,
            };

            $locked->update([
                'status' => $status,
                'outcome' => $tally['outcome'],
                'closed_at' => $closedAt,
                'result' => $tally + $extra + [
                    'statement' => self::statement($locked, $tally),
                    'closed_at' => $closedAt->toIso8601String(),
                ],
            ]);

            $this->log($locked, 'closed', $actor, ['outcome' => $tally['outcome'], 'voters' => $tally['voters']] + $extra);

            return true;
        });

        if ($done) {
            $motion->refresh();
            $this->notifyClosed($motion, true);
        }

        return $done;
    }

    // ---------------------------------------------------------------- notifications

    /**
     * Members who may vote on the motion (network access + eligibility).
     *
     * @return \Generator<int, User>
     */
    public function eligibleMembers(Motion $motion): \Generator
    {
        $query = User::query()->whereNotNull('email_verified_at')->orderBy('id');
        $rank = (int) $motion->min_tier_rank;

        foreach ($query->lazyById(200) as $user) {
            if ($this->access->canUseNetwork($user) && $this->access->canVote($user, $rank)) {
                yield $user;
            }
        }
    }

    /**
     * Tells eligible members a motion is open. With `$inlineOnly`, it defers when
     * there are many recipients (the scheduled pass picks it up via notified_at).
     */
    public function notifyOpened(Motion $motion, bool $inlineOnly = false): int
    {
        if ($motion->notified_at !== null || ! $motion->isOpenNow()) {
            return 0;
        }

        $recipients = [];
        foreach ($this->eligibleMembers($motion) as $user) {
            $recipients[] = $user;
            if ($inlineOnly && count($recipients) > self::INLINE_NOTIFY_LIMIT) {
                return 0;
            }
        }

        $motion->update(['notified_at' => now()]);

        return $this->fanOut($recipients, fn () => new MotionOpenedNotification($motion));
    }

    /** 24-hour reminder to eligible members who have not voted (once per motion). */
    public function sendClosingSoonReminder(Motion $motion): int
    {
        if ($motion->reminder_sent_at !== null || ! $motion->isOpenNow()) {
            return 0;
        }

        $motion->update(['reminder_sent_at' => now()]);

        // A motion open for a day or less needs no "closing soon" nudge on top of the opening notice.
        if ($motion->opens_at !== null && $motion->opens_at->diffInHours($motion->closes_at, true) <= 25) {
            return 0;
        }

        $voted = Vote::query()->where('motion_id', $motion->id)->pluck('user_id')->all();
        $recipients = [];
        foreach ($this->eligibleMembers($motion) as $user) {
            if (! in_array($user->id, $voted, true)) {
                $recipients[] = $user;
            }
        }

        return $this->fanOut($recipients, fn () => new MotionReminderNotification($motion));
    }

    /** Outcome email to voters and the creator. */
    public function notifyClosed(Motion $motion, bool $inlineOnly = false): int
    {
        if ($motion->closed_notified_at !== null || ! $motion->isDecided()) {
            return 0;
        }

        $ids = Vote::query()->where('motion_id', $motion->id)->pluck('user_id')->push($motion->created_by)->unique()->values();

        if ($inlineOnly && $ids->count() > self::INLINE_NOTIFY_LIMIT) {
            return 0;
        }

        $motion->update(['closed_notified_at' => now()]);

        $recipients = [];
        foreach ($ids->chunk(200) as $chunk) {
            foreach (User::query()->whereIn('id', $chunk)->get() as $user) {
                $recipients[] = $user;
            }
        }

        return $this->fanOut($recipients, fn () => new MotionClosedNotification($motion));
    }

    /** Work that cannot be done inside a request: used by `votes:close-due`. */
    public function runScheduledPass(): array
    {
        $closed = $this->closeDue();
        $opened = 0;
        $reminders = 0;
        $outcomes = 0;

        Motion::query()->where('status', Motion::OPEN)->whereNull('notified_at')->where('opens_at', '<=', now())->get()
            ->each(function (Motion $m) use (&$opened): void {
                $opened += $this->notifyOpened($m);
            });

        Motion::query()->where('status', Motion::OPEN)->whereNull('reminder_sent_at')
            ->where('opens_at', '<=', now())->where('closes_at', '<=', now()->addDay())->where('closes_at', '>', now())->get()
            ->each(function (Motion $m) use (&$reminders): void {
                $reminders += $this->sendClosingSoonReminder($m);
            });

        Motion::query()->whereIn('status', [Motion::PASSED, Motion::REJECTED, Motion::CLOSED])->whereNull('closed_notified_at')->get()
            ->each(function (Motion $m) use (&$outcomes): void {
                $outcomes += $this->notifyClosed($m);
            });

        return compact('closed', 'opened', 'reminders', 'outcomes');
    }

    /**
     * @param  list<User>  $recipients
     * @param  callable(): \Illuminate\Notifications\Notification  $make
     */
    private function fanOut(array $recipients, callable $make): int
    {
        $sent = 0;

        foreach (array_chunk($recipients, 100) as $chunk) {
            foreach ($chunk as $user) {
                try {
                    $user->notify($make());
                    $sent++;
                } catch (Throwable $e) {
                    Log::warning('Motion notification failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                }
            }
        }

        return $sent;
    }

    // ---------------------------------------------------------------- helpers

    /** @param  array<string, mixed>|null  $data */
    public function log(Motion $motion, string $event, ?User $user = null, ?array $data = null): MotionEvent
    {
        return MotionEvent::create([
            'motion_id' => $motion->id,
            'user_id' => $user?->id,
            'event' => $event,
            'data' => $data === [] ? null : $data,
        ]);
    }

    private function assertWindow(CarbonInterface $opens, ?CarbonInterface $closes): void
    {
        if ($closes === null || $closes->lt($opens->copy()->addHour())) {
            throw new MotionException('Voting must stay open for at least one hour: set a later closing time.');
        }

        if ($closes->gt($opens->copy()->addDays(90))) {
            throw new MotionException('Voting cannot stay open for more than 90 days.');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(array $data): array
    {
        $kind = (string) $data['kind'];

        return [
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'body' => $data['body'] ?? null,
            'kind' => $kind,
            'min_tier_rank' => (int) $data['min_tier_rank'],
            'choices' => $kind === 'decision' ? Motion::DEFAULT_CHOICES : array_values($data['choices']),
            'quorum' => (int) $data['quorum'],
            'pass_threshold' => $kind === 'decision' ? (float) ($data['pass_threshold'] ?? 50) : 50,
            'anonymous' => (bool) ($data['anonymous'] ?? false),
            'allow_change' => (bool) ($data['allow_change'] ?? true),
            'weighted' => (bool) ($data['weighted'] ?? false),
            'show_results' => $data['show_results'] ?? 'after_close',
            'opens_at' => $data['opens_at'] ?? null,
            'closes_at' => $data['closes_at'],
        ];
    }

    /**
     * Plain-language decision statement for a tally.
     *
     * @param  array<string, mixed>  $t
     */
    public static function statement(Motion $motion, array $t): string
    {
        $voters = (int) $t['voters'];
        $quorum = (int) $t['quorum'];
        $vs = $voters === 1 ? '1 member voted' : $voters.' members voted';
        $threshold = rtrim(rtrim(number_format((float) $t['threshold'], 2, '.', ''), '0'), '.');

        return match ($t['outcome']) {
            'no_quorum' => "No decision was taken: {$vs} but a quorum of {$quorum} was required.",
            'passed' => "The motion passed. {$t['for_share']}% of For and Against votes were For, meeting the {$threshold}% threshold, and {$vs} against a quorum of {$quorum}.",
            'rejected' => 'The motion was rejected. '.($t['for_share'] === null ? 'No For or Against votes were cast' : "Only {$t['for_share']}% of For and Against votes were For, below the {$threshold}% threshold").", and {$vs} against a quorum of {$quorum}.",
            'decided' => $t['winners'][0].' received the most votes ('.$t['counts'][$t['winners'][0]]." of {$t['weighted_total']}); {$vs} against a quorum of {$quorum}.",
            default => 'The vote ended without a single leading choice'.($t['winners'] !== [] ? ' (tied: '.implode(', ', $t['winners']).')' : '').". {$vs} against a quorum of {$quorum}.",
        };
    }

    /** One plain sentence describing how the motion is decided. */
    public static function rulesSentence(Motion $motion): string
    {
        $quorum = (int) $motion->quorum;
        $members = $quorum === 1 ? '1 member votes' : $quorum.' members vote';
        $weight = $motion->weighted ? ' Votes are weighted by membership tier.' : '';

        if ($motion->isDecision()) {
            $t = rtrim(rtrim(number_format((float) $motion->pass_threshold, 2, '.', ''), '0'), '.');

            return "Passes if at least {$t}% of For and Against votes are For and at least {$members}.{$weight}";
        }

        return "Valid if at least {$members}; the choice with the most votes wins.{$weight}";
    }

    /**
     * Safe formatted body: paragraphs, bullet lists, numbered lists and headings.
     * Returns plain data; the view escapes every string.
     *
     * @return list<array{type: string, text?: string, items?: list<string>}>
     */
    public static function bodyBlocks(?string $body): array
    {
        $blocks = [];
        $normalised = str_replace(["\r\n", "\r"], "\n", trim((string) $body));

        foreach (preg_split("/\n{2,}/", $normalised) ?: [] as $chunk) {
            $lines = array_values(array_filter(array_map('rtrim', explode("\n", $chunk)), fn ($l) => $l !== ''));

            if ($lines === []) {
                continue;
            }

            if (preg_match('/^#{1,3}\s+(.+)$/', $lines[0], $m) && count($lines) === 1) {
                $blocks[] = ['type' => 'heading', 'text' => $m[1]];
            } elseif (count(array_filter($lines, fn ($l) => preg_match('/^\s*[-*]\s+/', $l))) === count($lines)) {
                $blocks[] = ['type' => 'ul', 'items' => array_map(fn ($l) => preg_replace('/^\s*[-*]\s+/', '', $l), $lines)];
            } elseif (count(array_filter($lines, fn ($l) => preg_match('/^\s*\d+[.)]\s+/', $l))) === count($lines)) {
                $blocks[] = ['type' => 'ol', 'items' => array_map(fn ($l) => preg_replace('/^\s*\d+[.)]\s+/', '', $l), $lines)];
            } else {
                $blocks[] = ['type' => 'p', 'text' => implode("\n", array_map('trim', $lines))];
            }
        }

        return $blocks;
    }
}
