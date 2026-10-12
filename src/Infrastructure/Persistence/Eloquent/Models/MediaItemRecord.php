<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\MediaItemRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class MediaItemRecord extends Model
{
    /** @use HasFactory<MediaItemRecordFactory> */
    use HasFactory;

    protected $table = 'media_items';

    protected $guarded = [];

    protected $casts = [
        'position' => 'integer',
        'date' => 'date',
    ];

    protected static function newFactory(): MediaItemRecordFactory
    {
        return MediaItemRecordFactory::new();
    }
}
