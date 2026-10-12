<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\ConsultationRequestRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ConsultationRequestRecord extends Model
{
    /** @use HasFactory<ConsultationRequestRecordFactory> */
    use HasFactory;

    protected $table = 'consultation_requests';

    protected $guarded = [];

    protected $casts = [
        'submitted_at' => 'immutable_datetime',
        'has_supporting_documents' => 'boolean',
    ];

    /** Project/need descriptions are sensitive; keep them out of logs and stack traces. */
    protected $hidden = ['project_description', 'need_description'];

    protected static function newFactory(): ConsultationRequestRecordFactory
    {
        return ConsultationRequestRecordFactory::new();
    }
}
