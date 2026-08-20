<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class InsightRecord extends Model
{
    protected $table = 'insights';

    protected $guarded = [];

    protected $casts = ['published_at' => 'immutable_datetime'];
}
