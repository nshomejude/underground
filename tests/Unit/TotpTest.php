<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Totp;
use PHPUnit\Framework\TestCase;

final class TotpTest extends TestCase
{
    private const SECRET = '12345678901234567890';

    public function test_rfc6238_sha1_vectors(): void
    {
        $b32 = Totp::base32Encode(self::SECRET);
        $vectors = [59 => '94287082', 1111111109 => '07081804', 1111111111 => '14050471', 1234567890 => '89005924', 2000000000 => '69279037', 20000000000 => '65353130'];

        foreach ($vectors as $time => $expected8) {
            $this->assertSame($expected8, Totp::hotp(self::SECRET, intdiv($time, 30), 8), "time $time (8 digits)");
            $this->assertSame(substr($expected8, 2), Totp::code($b32, $time), "time $time (6 digits)");
        }
    }

    public function test_base32_round_trip_and_known_value(): void
    {
        $this->assertSame('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', Totp::base32Encode(self::SECRET));
        $this->assertSame(self::SECRET, Totp::base32Decode('gezd gnbv-GY3TQOJQGEZDGNBVGY3TQOJQ'));
        $secret = Totp::generateSecret();
        $this->assertSame(32, strlen($secret));
        $this->assertSame($secret, Totp::base32Encode(Totp::base32Decode($secret)));
    }

    public function test_verify_window_and_replay(): void
    {
        $b32 = Totp::base32Encode(self::SECRET);
        $t = 1111111109;
        $step = Totp::timeStep($t);

        $this->assertSame($step, Totp::verify($b32, Totp::code($b32, $t), null, $t));
        $this->assertSame($step - 1, Totp::verify($b32, Totp::code($b32, $t - 30), null, $t));
        $this->assertSame($step + 1, Totp::verify($b32, Totp::code($b32, $t + 30), null, $t));
        $this->assertNull(Totp::verify($b32, Totp::code($b32, $t - 90), null, $t));
        $this->assertNull(Totp::verify($b32, Totp::code($b32, $t), $step, $t), 'replay of used step');
        $this->assertNull(Totp::verify($b32, 'abcdef', null, $t));
    }

    public function test_uri_format(): void
    {
        $uri = Totp::uri('a@b.co', 'ABC', 'Underground Network');
        $this->assertSame('otpauth://totp/Underground%20Network:a%40b.co?secret=ABC&issuer=Underground%20Network&algorithm=SHA1&digits=6&period=30', $uri);
    }
}
