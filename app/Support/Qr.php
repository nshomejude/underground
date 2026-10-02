<?php

declare(strict_types=1);

namespace App\Support;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Server-side QR code as inline SVG (no GD / imagick, no JavaScript).
 * Error correction level M with a 2-module quiet zone; dark modules in the
 * brand ink on a transparent background so the caller controls the backing.
 */
final class Qr
{
    public static function svg(string $data, string $dark = '#0B0B0C'): string
    {
        $options = new QROptions([
            'version' => QRCode::VERSION_AUTO,
            'eccLevel' => EccLevel::M,
            'outputType' => QROutputInterface::MARKUP_SVG,
            'outputBase64' => false,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
            'drawLightModules' => false,
            'svgUseFillAttributes' => true,
            'connectPaths' => true,
            'svgPreserveAspectRatio' => 'xMidYMid meet',
            'moduleValues' => [
                QRMatrix::IS_DARK => $dark,
            ],
        ]);

        return (new QRCode($options))->render($data);
    }
}
