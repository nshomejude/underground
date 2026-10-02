<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MembershipCertificate;
use Domain\Membership\Entities\MembershipApplication;
use Domain\Membership\ValueObjects\MembershipApplicationStatus;
use Illuminate\Support\Str;

/**
 * Issues (idempotently) and looks up membership certificates.
 *
 * A certificate exists only for an approved application. The issue date is
 * the application's submission date until an explicit approval timestamp is
 * stored; the certificate is valid for one year from issue.
 */
final class CertificateService
{
    public function ensureFor(MembershipApplication $application): ?MembershipCertificate
    {
        if ($application->status() !== MembershipApplicationStatus::Approved || $application->memberId() === null) {
            return null;
        }

        $reference = $application->reference->value;

        $existing = MembershipCertificate::query()->where('application_reference', $reference)->latest('id')->first();

        if ($existing !== null) {
            return $existing;
        }

        $issuedAt = $application->submittedAt;

        $certificate = MembershipCertificate::query()->create([
            'application_reference' => $reference,
            'member_id' => $application->memberId()->value,
            'verify_token' => Str::random(24),
            'issued_at' => $issuedAt,
            'valid_through' => $issuedAt->modify('+1 year'),
        ]);

        $certificate->forceFill([
            'serial' => sprintf('UGC-%s-%06d', $issuedAt->format('Y'), $certificate->id),
        ])->save();

        return $certificate;
    }

    public function findByToken(string $token): ?MembershipCertificate
    {
        if (preg_match('/^[A-Za-z0-9]{24}$/', $token) !== 1) {
            return null;
        }

        return MembershipCertificate::query()->where('verify_token', $token)->first();
    }
}
