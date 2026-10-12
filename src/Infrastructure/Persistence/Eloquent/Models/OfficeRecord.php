<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\OfficeRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class OfficeRecord extends Model
{
    /** @use HasFactory<OfficeRecordFactory> */
    use HasFactory;

    protected $table = 'offices';

    protected $guarded = [];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'countries' => 'array',
        'is_headquarters' => 'boolean',
        'position' => 'integer',
    ];

    protected static function newFactory(): OfficeRecordFactory
    {
        return OfficeRecordFactory::new();
    }
}
