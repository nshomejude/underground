<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\InsightRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class InsightRecord extends Model
{
    /** @use HasFactory<InsightRecordFactory> */
    use HasFactory;

    protected $table = 'insights';

    protected $guarded = [];

    protected $casts = ['published_at' => 'immutable_datetime'];

    protected static function newFactory(): InsightRecordFactory
    {
        return InsightRecordFactory::new();
    }
}
