<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

/**
 * Code 128 (subset B) barcode as inline SVG — printable ASCII 32–126 only.
 * Dependency-free so it works on shared hosting. The output is a row of
 * <rect> bars sized in module units, scaled by the SVG viewBox.
 */
final class Code128
{
    /** Bar/space widths for symbol values 0–106 (start A/B/C = 103/104/105, stop = 106). */
    private const PATTERNS = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112',
    ];

    private const START_B = 104;

    private const STOP = 106;

    /** The bar/space module sequence ("1" = bar, "0" = space) for the given text. */
    public static function modules(string $text): string
    {
        if ($text === '' || preg_match('/^[\x20-\x7E]+$/', $text) !== 1) {
            throw new InvalidArgumentException('Code 128B encodes printable ASCII only.');
        }

        $codes = [self::START_B];
        $checksum = self::START_B;

        foreach (str_split($text) as $i => $char) {
            $value = ord($char) - 32;
            $codes[] = $value;
            $checksum += $value * ($i + 1);
        }

        $codes[] = $checksum % 103;
        $codes[] = self::STOP;

        $bits = '';
        foreach ($codes as $code) {
            $isBar = true;
            foreach (str_split(self::PATTERNS[$code]) as $width) {
                $bits .= str_repeat($isBar ? '1' : '0', (int) $width);
                $isBar = ! $isBar;
            }
        }

        return $bits;
    }

    public static function svg(string $text, string $color = '#0B0B0C', int $height = 40): string
    {
        $bits = self::modules($text);
        $quiet = 10;
        $width = strlen($bits) + 2 * $quiet;
        $bars = '';
        $run = null;

        for ($i = 0, $n = strlen($bits); $i <= $n; $i++) {
            $isBar = $i < $n && $bits[$i] === '1';

            if ($isBar && $run === null) {
                $run = $i;
            } elseif (! $isBar && $run !== null) {
                $bars .= '<rect x="'.($run + $quiet).'" y="0" width="'.($i - $run).'" height="'.$height.'"/>';
                $run = null;
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$width.' '.$height.'" preserveAspectRatio="none" role="img" aria-label="Barcode '.htmlspecialchars($text, ENT_QUOTES).'" fill="'.$color.'" shape-rendering="crispEdges">'.$bars.'</svg>';
    }
}
