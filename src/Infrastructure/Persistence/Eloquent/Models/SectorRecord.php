<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class SectorRecord extends Model
{
    protected $table = 'sectors';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];
}
