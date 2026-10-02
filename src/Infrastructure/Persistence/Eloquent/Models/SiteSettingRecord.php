<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class SiteSettingRecord extends Model
{
    protected $table = 'site_settings';

    protected $guarded = [];

    protected $casts = [
        'social_links' => 'array',
        'maintenance_mode' => 'boolean',
        'public_registration_enabled' => 'boolean',
        'gear_animation_enabled' => 'boolean',
        'network_animation_enabled' => 'boolean',
    ];
}
