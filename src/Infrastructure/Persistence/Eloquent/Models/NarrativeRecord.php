<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\NarrativeRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Persistence shape for the firm's authored brand copy. Never leaves the
 * infrastructure layer — the repository maps it to Domain\Content\Entities\Narrative.
 */
final class NarrativeRecord extends Model
{
    /** @use HasFactory<NarrativeRecordFactory> */
    use HasFactory;

    protected $table = 'narratives';

    protected $guarded = [];

    protected $casts = [
        'headline' => 'array',
        'primary_cta' => 'array',
        'secondary_cta' => 'array',
        'reach_cta' => 'array',
        'closing_cta' => 'array',
        'navigation' => 'array',
    ];

    protected static function newFactory(): NarrativeRecordFactory
    {
        return NarrativeRecordFactory::new();
    }
}
