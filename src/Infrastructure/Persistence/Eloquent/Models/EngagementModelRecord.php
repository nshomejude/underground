<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\EngagementModelRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class EngagementModelRecord extends Model
{
    /** @use HasFactory<EngagementModelRecordFactory> */
    use HasFactory;

    protected $table = 'engagement_models';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];

    protected static function newFactory(): EngagementModelRecordFactory
    {
        return EngagementModelRecordFactory::new();
    }
}
