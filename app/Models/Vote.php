<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\VoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per member per motion (unique), which prevents double voting.
 * For anonymous motions the row still exists but no view, export or audit
 * entry ever shows which member chose what.
 */
final class Vote extends Model
{
    /** @use HasFactory<VoteFactory> */
    use HasFactory;

    protected $guarded = [];

    public function motion(): BelongsTo
    {
        return $this->belongsTo(Motion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
