<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PlanChangeRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PlanChangeRequest extends Model
{
    /** @use HasFactory<PlanChangeRequestFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toPlan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'to_plan_id');
    }

    public function fromPlan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'from_plan_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
