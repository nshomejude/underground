<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\ProjectRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ProjectRecord extends Model
{
    /** @use HasFactory<ProjectRecordFactory> */
    use HasFactory;

    protected $table = 'projects';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];

    protected static function newFactory(): ProjectRecordFactory
    {
        return ProjectRecordFactory::new();
    }
}
