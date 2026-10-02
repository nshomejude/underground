<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

/**
 * Dependency-free RFC 6238 TOTP (HMAC-SHA1, 6 digits, 30 second step) with
 * RFC 4648 Base32 helpers. Verification is constant-time and replay-aware:
 * the caller passes the last accepted time-step and any step <= it is refused.
 */
final class Totp
{
    public const DIGITS = 6;

    public const PERIOD = 30;

    public const WINDOW = 1;

    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /** A fresh 160-bit secret, Base32 encoded (32 characters). */
    public static function generateSecret(): string
    {
        return self::base32Encode(random_bytes(20));
    }

    public static function base32Encode(string $bytes): string
    {
        $bits = '';
        foreach (str_split($bytes) as $char) {
            $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            $out .= self::ALPHABET[bindec(str_pad($chunk, 5, '0'))];
        }

        return $out;
    }

    public static function base32Decode(string $encoded): string
    {
        $encoded = strtoupper(preg_replace('/[\s=-]+/', '', $encoded) ?? '');
        $bits = '';
        foreach (str_split($encoded) as $char) {
            $pos = strpos(self::ALPHABET, $char);
            if ($pos === false) {
                throw new InvalidArgumentException('Invalid Base32 character.');
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }

        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $out .= chr((int) bindec($byte));
            }
        }

        return $out;
    }

    public static function timeStep(?int $timestamp = null): int
    {
        return intdiv($timestamp ?? time(), self::PERIOD);
    }

    /** The code for a given counter, from a raw (binary) key. */
    public static function hotp(string $rawKey, int $counter, int $digits = self::DIGITS): string
    {
        $hash = hash_hmac('sha1', pack('J', $counter), $rawKey, true);
        $offset = ord($hash[19]) & 0x0F;
        $binary = ((ord($hash[$offset]) & 0x7F) << 24)
            | (ord($hash[$offset + 1]) << 16)
            | (ord($hash[$offset + 2]) << 8)
            | ord($hash[$offset + 3]);

        return str_pad((string) ($binary % (10 ** $digits)), $digits, '0', STR_PAD_LEFT);
    }

    public static function code(string $base32Secret, ?int $timestamp = null): string
    {
        return self::hotp(self::base32Decode($base32Secret), self::timeStep($timestamp));
    }

    /**
     * Returns the matched time-step, or null when the code is invalid or its
     * step is not strictly newer than $lastStep (replay protection).
     */
    public static function verify(string $base32Secret, string $code, ?int $lastStep = null, ?int $timestamp = null): ?int
    {
        $code = preg_replace('/\s+/', '', $code) ?? '';
        if (preg_match('/^\d{6}$/', $code) !== 1) {
            return null;
        }

        $key = self::base32Decode($base32Secret);
        $current = self::timeStep($timestamp);
        $matched = null;

        // Evaluate every window slot so timing does not reveal which matched.
        for ($step = $current - self::WINDOW; $step <= $current + self::WINDOW; $step++) {
            $hit = hash_equals(self::hotp($key, $step), $code);
            if ($hit && $matched === null && ($lastStep === null || $step > $lastStep)) {
                $matched = $step;
            }
        }

        return $matched;
    }

    public static function uri(string $email, string $base32Secret, string $issuer): string
    {
        return 'otpauth://totp/'.rawurlencode($issuer).':'.rawurlencode($email)
            .'?secret='.$base32Secret
            .'&issuer='.rawurlencode($issuer)
            .'&algorithm=SHA1&digits='.self::DIGITS.'&period='.self::PERIOD;
    }
}
