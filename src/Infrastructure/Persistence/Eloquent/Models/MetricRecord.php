<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\MetricRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class MetricRecord extends Model
{
    /** @use HasFactory<MetricRecordFactory> */
    use HasFactory;

    protected $table = 'metrics';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];

    protected static function newFactory(): MetricRecordFactory
    {
        return MetricRecordFactory::new();
    }
}
