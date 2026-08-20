<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class EngagementModelRecord extends Model
{
    protected $table = 'engagement_models';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];
}
