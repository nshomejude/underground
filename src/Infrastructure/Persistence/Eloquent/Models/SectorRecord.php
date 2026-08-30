<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\SectorRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SectorRecord extends Model
{
    /** @use HasFactory<SectorRecordFactory> */
    use HasFactory;

    protected $table = 'sectors';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];

    protected static function newFactory(): SectorRecordFactory
    {
        return SectorRecordFactory::new();
    }
}
