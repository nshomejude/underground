<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MembershipApplicationRecord extends Model
{
    protected $table = 'membership_applications';

    protected $guarded = [];

    protected $casts = ['submitted_at' => 'immutable_datetime'];

    /** Statements are sensitive; keep them out of logs and stack traces. */
    protected $hidden = ['statement'];

    /** @return BelongsTo<MembershipTierRecord, $this> */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(MembershipTierRecord::class, 'tier_id');
    }
}
