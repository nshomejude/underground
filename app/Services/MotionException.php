<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/** A voting rule was broken; the message is safe to show to the member. */
final class MotionException extends RuntimeException {}
