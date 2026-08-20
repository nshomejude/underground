<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class MetricRecord extends Model
{
    protected $table = 'metrics';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];
}
