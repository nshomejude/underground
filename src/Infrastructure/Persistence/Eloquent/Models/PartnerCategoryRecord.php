<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\PartnerCategoryRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class PartnerCategoryRecord extends Model
{
    /** @use HasFactory<PartnerCategoryRecordFactory> */
    use HasFactory;

    protected $table = 'partner_categories';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];

    protected static function newFactory(): PartnerCategoryRecordFactory
    {
        return PartnerCategoryRecordFactory::new();
    }
}
