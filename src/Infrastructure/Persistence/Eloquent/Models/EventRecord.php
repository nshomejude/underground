<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\EventRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class EventRecord extends Model
{
    /** @use HasFactory<EventRecordFactory> */
    use HasFactory;

    protected $table = 'events';

    protected $guarded = [];

    protected $casts = [
        'position' => 'integer',
        'date' => 'date',
    ];

    protected static function newFactory(): EventRecordFactory
    {
        return EventRecordFactory::new();
    }
}
