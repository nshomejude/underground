<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ConnectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Connection extends Model
{
    /** @use HasFactory<ConnectionFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'shared_sectors' => 'array',
            'responded_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function addressee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'addressee_id');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function involves(User $user): bool
    {
        return $this->requester_id === $user->id || $this->addressee_id === $user->id;
    }

    public function otherParty(User $user): User
    {
        return $this->requester_id === $user->id ? $this->addressee : $this->requester;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isCollaboration(): bool
    {
        return $this->kind === 'collaborate';
    }

    /** Connections between two users, whichever direction the row runs. */
    public function scopeBetween(Builder $query, int $a, int $b): Builder
    {
        return $query->where(function (Builder $q) use ($a, $b): void {
            $q->where(fn (Builder $x) => $x->where('requester_id', $a)->where('addressee_id', $b))
                ->orWhere(fn (Builder $x) => $x->where('requester_id', $b)->where('addressee_id', $a));
        });
    }

    /** Rows that involve the user on either side. */
    public function scopeInvolving(Builder $query, int $userId): Builder
    {
        return $query->where(fn (Builder $q) => $q->where('requester_id', $userId)->orWhere('addressee_id', $userId));
    }
}
