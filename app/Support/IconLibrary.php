<?php

declare(strict_types=1);

namespace App\Support;

/**
 * The Lucide icon names whitelisted in resources/views/components/icon.blade.php.
 * Kept here so admin forms can validate an icon choice against exactly the
 * set the icon component actually knows how to render, without either side
 * having to parse the other.
 */
final class IconLibrary
{
    /** @var list<string> */
    public const NAMES = [
        'globe', 'handshake', 'flag', 'landmark', 'shield-check', 'radar',
        'coins', 'megaphone', 'ship-wheel', 'library', 'target', 'gem',
        'building-2', 'menu', 'x', 'chevron-right', 'chevron-down', 'home',
        'newspaper', 'mail', 'phone', 'map-pin', 'lock', 'check-circle',
        'clock', 'arrow-right', 'user', 'users', 'briefcase', 'rotate-cw',
        'scan-line', 'flashlight',
    ];
}
