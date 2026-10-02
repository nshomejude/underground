<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MotionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Motion extends Model
{
    /** @use HasFactory<MotionFactory> */
    use HasFactory;

    public const DRAFT = 'draft';

    public const OPEN = 'open';

    public const PASSED = 'passed';

    public const REJECTED = 'rejected';

    public const CLOSED = 'closed';

    public const CANCELLED = 'cancelled';

    public const TIER_LABELS = [
        1 => 'All members',
        2 => 'Principal Circle and above',
        3 => 'Sovereign Partners only',
    ];

    public const DEFAULT_CHOICES = ['For', 'Against', 'Abstain'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'choices' => 'array',
            'result' => 'array',
            'anonymous' => 'boolean',
            'weighted' => 'boolean',
            'allow_change' => 'boolean',
            'pass_threshold' => 'float',
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
            'closed_at' => 'datetime',
            'published_at' => 'datetime',
            'notified_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'closed_notified_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(MotionEvent::class);
    }

    public function isDecision(): bool
    {
        return $this->kind === 'decision';
    }

    /** @return list<string> */
    public function choiceList(): array
    {
        $choices = $this->choices;

        return is_array($choices) && $choices !== [] ? array_values($choices) : self::DEFAULT_CHOICES;
    }

    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::CANCELLED;
    }

    /** Published and waiting for opens_at. */
    public function isScheduled(): bool
    {
        return $this->status === self::OPEN && $this->opens_at !== null && $this->opens_at->isFuture();
    }

    /** Accepting votes right now. */
    public function isOpenNow(): bool
    {
        return $this->status === self::OPEN
            && ($this->opens_at === null || $this->opens_at->lte(now()))
            && $this->closes_at !== null && $this->closes_at->isFuture();
    }

    /** Closed with an outcome (passed, rejected, or closed without a binding result). */
    public function isDecided(): bool
    {
        return in_array($this->status, [self::PASSED, self::REJECTED, self::CLOSED], true);
    }

    public function tierLabel(): string
    {
        return self::TIER_LABELS[(int) $this->min_tier_rank] ?? 'All members';
    }

    public function kindLabel(): string
    {
        return ucfirst((string) $this->kind);
    }
}
