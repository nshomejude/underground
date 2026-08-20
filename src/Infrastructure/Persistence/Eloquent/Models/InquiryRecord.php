<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class InquiryRecord extends Model
{
    protected $table = 'confidential_inquiries';

    protected $guarded = [];

    protected $casts = ['submitted_at' => 'immutable_datetime'];

    /** Briefs are sensitive; keep them out of logs and stack traces. */
    protected $hidden = ['brief'];
}
