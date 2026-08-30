<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\MembershipTierRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class MembershipTierRecord extends Model
{
    /** @use HasFactory<MembershipTierRecordFactory> */
    use HasFactory;

    protected $table = 'membership_tiers';

    protected $guarded = [];

    protected static function newFactory(): MembershipTierRecordFactory
    {
        return MembershipTierRecordFactory::new();
    }
}
