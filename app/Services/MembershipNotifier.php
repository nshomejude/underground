<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Notifications\MembershipStatusNotification;
use Application\Membership\Queries\ListMembershipTiers;
use Domain\Membership\Entities\MembershipApplication;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Tells an applicant about a milestone in their membership application:
 * by email always, and into their dashboard when they have an account.
 * A failure to notify must never break the action that triggered it.
 */
final class MembershipNotifier
{
    public function __construct(
        private readonly CertificateService $certificates,
        private readonly ListMembershipTiers $tiers,
    ) {}

    /** @param  'received'|'approved'|'declined'  $stage */
    public function notify(MembershipApplication $application, string $stage): void
    {
        try {
            $context = [
                'reference' => $application->reference->value,
                'tier' => $this->tiers->bySlug($application->tier->value)?->name ?? 'Underground',
                'name' => $application->name,
            ];

            if ($stage === 'approved') {
                $certificate = $this->certificates->ensureFor($application);
                $context['memberId'] = $application->memberId()?->value;
                $context['serial'] = $certificate?->serial;
                $context['verifyUrl'] = $certificate?->verificationUrl();
            }

            $notification = new MembershipStatusNotification($stage, $context);
            $email = $application->email->value;

            $user = User::query()->whereRaw('lower(email) = ?', [strtolower($email)])->first();

            if ($user !== null) {
                $user->notify($notification);

                return;
            }

            Notification::route('mail', $email)->notify($notification);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
