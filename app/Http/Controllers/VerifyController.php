<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CertificateService;
use Application\Membership\Queries\ListMembershipTiers;
use Application\Membership\Queries\TrackMembershipApplication;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\Response;

/**
 * Public credential verification, reached by scanning the QR code on a
 * membership card or certificate. Shows the minimum needed to verify:
 * a display name (first name and last initial for individuals), the tier,
 * serial, status and dates — never contact details or the member id.
 */
final class VerifyController extends Controller
{
    public function __construct(
        private readonly CertificateService $certificates,
        private readonly TrackMembershipApplication $track,
        private readonly ListMembershipTiers $tiers,
    ) {}

    public function show(string $token): Response
    {
        $certificate = $this->certificates->findByToken($token);

        $state = 'not_found';
        $facts = null;

        if ($certificate !== null) {
            $application = null;

            try {
                $application = ($this->track)($certificate->application_reference);
            } catch (DomainException) {
                $application = null;
            }

            if ($application !== null) {
                $state = $certificate->status();
                $tier = $this->tiers->bySlug($application->tier->value);

                $facts = [
                    'name' => self::displayName($application->name, $application->organisation),
                    'isOrganisation' => $application->organisation !== null,
                    'tier' => $tier?->name ?? 'Member',
                    'serial' => $certificate->serial,
                    'issuedAt' => $certificate->issued_at,
                    'validThrough' => $certificate->valid_through,
                    'revokedAt' => $certificate->revoked_at,
                    'fingerprint' => substr(hash('sha256', $certificate->serial.'|'.$certificate->verify_token), 0, 12),
                ];
            }
        }

        $response = response()->view('verify.show', [
            'state' => $state,
            'facts' => $facts,
            'checkedAt' => now(),
            'token' => $token,
        ], $state === 'not_found' ? 404 : 200);

        return $response->withHeaders([
            'X-Robots-Tag' => 'noindex, nofollow',
            'Cache-Control' => 'no-store, private',
            'Referrer-Policy' => 'no-referrer',
        ]);
    }

    /** Organisations show their name; individuals show first name and last initial. */
    private static function displayName(string $name, ?string $organisation): string
    {
        if ($organisation !== null) {
            return $organisation;
        }

        $parts = preg_split('/\s+/', trim($name)) ?: [];

        if (count($parts) < 2) {
            return $parts[0] ?? 'Member';
        }

        return $parts[0].' '.mb_strtoupper(mb_substr(end($parts), 0, 1)).'.';
    }
}
