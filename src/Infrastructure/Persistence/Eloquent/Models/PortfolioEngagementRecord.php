<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\PortfolioEngagementRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class PortfolioEngagementRecord extends Model
{
    /** @use HasFactory<PortfolioEngagementRecordFactory> */
    use HasFactory;

    protected $table = 'portfolio_engagements';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];

    protected static function newFactory(): PortfolioEngagementRecordFactory
    {
        return PortfolioEngagementRecordFactory::new();
    }
}
