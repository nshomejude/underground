<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\TeamMemberRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class TeamMemberRecord extends Model
{
    /** @use HasFactory<TeamMemberRecordFactory> */
    use HasFactory;

    protected $table = 'team_members';

    protected $guarded = [];

    protected $casts = [
        'position' => 'integer',
        'has_portrait' => 'boolean',
    ];

    protected static function newFactory(): TeamMemberRecordFactory
    {
        return TeamMemberRecordFactory::new();
    }
}
