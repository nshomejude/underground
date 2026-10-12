<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\GalleryPhotoRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class GalleryPhotoRecord extends Model
{
    /** @use HasFactory<GalleryPhotoRecordFactory> */
    use HasFactory;

    protected $table = 'gallery_photos';

    protected $guarded = [];

    protected $casts = ['position' => 'integer'];

    protected static function newFactory(): GalleryPhotoRecordFactory
    {
        return GalleryPhotoRecordFactory::new();
    }
}
