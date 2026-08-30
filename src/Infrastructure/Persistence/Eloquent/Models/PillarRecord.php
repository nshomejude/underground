<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\PillarRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class PillarRecord extends Model
{
    /** @use HasFactory<PillarRecordFactory> */
    use HasFactory;

    protected $table = 'pillars';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];

    protected static function newFactory(): PillarRecordFactory
    {
        return PillarRecordFactory::new();
    }
}
