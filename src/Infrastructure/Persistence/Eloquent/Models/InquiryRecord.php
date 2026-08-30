<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\InquiryRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class InquiryRecord extends Model
{
    /** @use HasFactory<InquiryRecordFactory> */
    use HasFactory;

    protected $table = 'confidential_inquiries';

    protected $guarded = [];

    protected $casts = ['submitted_at' => 'immutable_datetime'];

    /** Briefs are sensitive; keep them out of logs and stack traces. */
    protected $hidden = ['brief'];

    protected static function newFactory(): InquiryRecordFactory
    {
        return InquiryRecordFactory::new();
    }
}
