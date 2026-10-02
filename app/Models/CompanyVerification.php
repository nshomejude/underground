<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CompanyVerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class CompanyVerification extends Model
{
    use HasFactory;

    public const KIND = 'company';

    protected $guarded = [];

    protected static function newFactory(): CompanyVerificationFactory
    {
        return CompanyVerificationFactory::new();
    }

    protected function casts(): array
    {
        return [
            'sectors' => 'array',
            'documents' => 'array',
            'incorporation_date' => 'date',
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

    public function events(): HasMany
    {
        return $this->hasMany(VerificationEvent::class, 'verification_id')
            ->where('kind', self::KIND)
            ->oldest('id');
    }
}
