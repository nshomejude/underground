<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ConversationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Conversation extends Model
{
    /** @use HasFactory<ConversationFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Connection::class, 'connection_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /** The two parties may talk only while their connection is accepted (not blocked, withdrawn or pending). */
    public function isOpen(): bool
    {
        return $this->link?->isAccepted() === true;
    }

    public function hasParty(User $user): bool
    {
        return $this->link?->involves($user) === true;
    }
}
