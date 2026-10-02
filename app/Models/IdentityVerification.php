<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\IdentityVerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class IdentityVerification extends Model
{
    use HasFactory;

    public const KIND = 'identity';

    protected $guarded = [];

    protected $hidden = ['date_of_birth_encrypted'];

    protected static function newFactory(): IdentityVerificationFactory
    {
        return IdentityVerificationFactory::new();
    }

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'document_expiry' => 'date',
            'consent_at' => 'datetime',
            'in_review_at' => 'datetime',
            'files_purged_at' => 'datetime',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved' && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /** Draft, submitted or in review: the one submission a member may have open. */
    public function isActive(): bool
    {
        return in_array($this->status, ['draft', 'submitted', 'in_review'], true);
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    /** Decrypted date of birth (Y-m-d) or null; the ciphertext is the only thing stored. */
    public function dateOfBirth(): ?string
    {
        if ($this->date_of_birth_encrypted === null) {
            return null;
        }

        try {
            return decrypt($this->date_of_birth_encrypted);
        } catch (\Throwable) {
            return null;
        }
    }

    public function events(): HasMany
    {
        return $this->hasMany(VerificationEvent::class, 'verification_id')
            ->where('kind', self::KIND)
            ->oldest('id');
    }
}
