<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Closure;
use Illuminate\Http\UploadedFile;

/** Shared server-side rules for verification uploads: real mime (finfo), size, image dimensions. */
final class VerificationFileRules
{
    public const IMAGES = 'image/jpeg,image/png,image/webp';

    public const DOCS = 'image/jpeg,image/png,image/webp,application/pdf';

    /** @return list<mixed> */
    public static function rules(bool $allowPdf, int $maxKb, bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'mimetypes:'.($allowPdf ? self::DOCS : self::IMAGES),
            'max:'.$maxKb,
            self::minDimension(),
        ];
    }

    public static function minDimension(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! $value instanceof UploadedFile || ! str_starts_with((string) $value->getMimeType(), 'image/')) {
                return;
            }
            $info = @getimagesize($value->getRealPath());
            $min = (int) config('verification.min_image_dimension', 600);
            if ($info === false) {
                $fail('This image could not be read. Please upload a different file.');

                return;
            }
            if (max($info[0], $info[1]) < $min) {
                $fail("This image is too small ({$info[0]} x {$info[1]} px). The long side must be at least {$min} px so the text is readable.");
            }
        };
    }

    /** @return array<string, string> */
    public static function messages(string $attribute): array
    {
        return [
            $attribute.'.mimetypes' => 'Unsupported or mismatched file type. Use JPG, PNG, WebP or PDF (selfie: images only).',
            $attribute.'.max' => 'This file is too large.',
            $attribute.'.uploaded' => 'The upload failed. It may be larger than the server allows.',
        ];
    }
}
