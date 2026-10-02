<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MemberProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

final class MemberProfile extends Model
{
    /** @use HasFactory<MemberProfileFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'sectors' => 'array',
            'supply_chain_roles' => 'array',
            'seeking_kinds' => 'array',
            'services' => 'array',
            'portfolio' => 'array',
            'open_to_collaboration' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path !== null && $this->avatar_path !== ''
            ? Storage::disk('public')->url($this->avatar_path)
            : null;
    }
}
