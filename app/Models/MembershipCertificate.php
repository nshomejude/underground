<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One issued membership certificate. The public `verify_token` (never the
 * member id or serial) is what the QR code encodes, so the verification URL
 * cannot be guessed or enumerated.
 */
final class MembershipCertificate extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'valid_through' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function status(): string
    {
        if ($this->revoked_at !== null) {
            return 'revoked';
        }

        return $this->valid_through->isPast() ? 'expired' : 'valid';
    }

    public function verificationUrl(): string
    {
        return route('verify.show', ['token' => $this->verify_token]);
    }
}
