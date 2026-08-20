<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Persistence shape for a capability. Never leaves the infrastructure layer —
 * repositories map it to the domain entity.
 */
final class CapabilityRecord extends Model
{
    protected $table = 'capabilities';

    protected $guarded = [];

    protected $casts = [
        'position' => 'integer',
        'is_featured' => 'boolean',
    ];
}
