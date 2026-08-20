<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class PillarRecord extends Model
{
    protected $table = 'pillars';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];
}
