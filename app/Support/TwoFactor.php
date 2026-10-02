<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Two-factor state transitions for a User: enrolment, code verification
 * (TOTP with replay protection, single-use recovery codes) and teardown.
 */
final class TwoFactor
{
    public const RECOVERY_COUNT = 8;

    private const CODE_ALPHABET = 'abcdefghjkmnpqrstuvwxyz23456789';

    public static function beginEnrolment(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => encrypt(Totp::generateSecret()),
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_last_step' => null,
        ])->save();
    }

    /** Verifies a code, burning its time-step atomically (replay protection). */
    public static function verifyCode(User $user, string $code): bool
    {
        $secret = $user->twoFactorSecret();
        if ($secret === null) {
            return false;
        }

        $last = $user->two_factor_last_step === null ? null : (int) $user->two_factor_last_step;
        $step = Totp::verify($secret, $code, $last);
        if ($step === null) {
            return false;
        }

        $claimed = User::query()
            ->whereKey($user->getKey())
            ->where(fn ($q) => $q->whereNull('two_factor_last_step')->orWhere('two_factor_last_step', '<', $step))
            ->update(['two_factor_last_step' => $step]);

        if ($claimed !== 1) {
            return false;
        }

        $user->forceFill(['two_factor_last_step' => $step])->syncOriginal();

        return true;
    }

    public static function confirm(User $user): void
    {
        $user->forceFill(['two_factor_confirmed_at' => now()])->save();
    }

    /**
     * Replaces all recovery codes. Returns the plaintext codes (shown once).
     *
     * @return list<string>
     */
    public static function regenerateRecoveryCodes(User $user): array
    {
        $plain = [];
        for ($i = 0; $i < self::RECOVERY_COUNT; $i++) {
            $plain[] = self::randomChunk(5).'-'.self::randomChunk(5);
        }

        $user->forceFill([
            'two_factor_recovery_codes' => json_encode(array_map(self::hash(...), $plain), JSON_THROW_ON_ERROR),
        ])->save();

        return $plain;
    }

    /** Checks and consumes a recovery code; compares against every stored hash in constant time. */
    public static function consumeRecoveryCode(User $user, string $input): bool
    {
        $candidate = self::hash($input);

        return DB::transaction(function () use ($user, $candidate): bool {
            $fresh = User::query()->whereKey($user->getKey())->lockForUpdate()->first();
            if ($fresh === null) {
                return false;
            }

            $hashes = $fresh->recoveryCodeHashes();
            $matchIndex = null;
            foreach ($hashes as $i => $hash) {
                if (hash_equals((string) $hash, $candidate)) {
                    $matchIndex = $i;
                }
            }

            if ($matchIndex === null) {
                return false;
            }

            unset($hashes[$matchIndex]);
            $fresh->forceFill(['two_factor_recovery_codes' => json_encode(array_values($hashes), JSON_THROW_ON_ERROR)])->save();
            $user->forceFill(['two_factor_recovery_codes' => $fresh->two_factor_recovery_codes])->syncOriginal();

            return true;
        });
    }

    public static function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_last_step' => null,
        ])->save();
    }

    private static function hash(string $code): string
    {
        $normalised = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $code) ?? '');

        return hash_hmac('sha256', $normalised, (string) config('app.key'));
    }

    private static function randomChunk(int $length): string
    {
        $out = '';
        $max = strlen(self::CODE_ALPHABET) - 1;
        for ($i = 0; $i < $length; $i++) {
            $out .= self::CODE_ALPHABET[random_int(0, $max)];
        }

        return $out;
    }
}
