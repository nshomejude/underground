<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * One notification for the milestones of a membership application:
 *
 *   received  application accepted into the review queue
 *   approved  approved: membership card + certificate are issued
 *   declined  decision communicated courteously
 *
 * Members with an account also get it in their dashboard (database
 * channel); applicants without one get the email only.
 */
final class MembershipStatusNotification extends Notification
{
    /**
     * @param  array{reference: string, tier: string, name: string, memberId?: ?string, serial?: ?string, verifyUrl?: ?string}  $context
     */
    public function __construct(public readonly string $stage, public readonly array $context) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $copy = $this->copy();

        return BrandedMessage::make(
            subject: $copy['subject'],
            heading: $copy['title'],
            intro: $copy['intro'],
            actionText: $copy['actionText'],
            actionUrl: $copy['actionUrl'],
            outro: $copy['outro'],
            eyebrow: $copy['eyebrow'],
            preheader: $copy['preheader'],
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $copy = $this->copy();

        return [
            'stage' => $this->stage,
            'title' => $copy['title'],
            'body' => $copy['preheader'],
            'url' => $copy['actionUrl'],
            'reference' => $this->context['reference'],
        ];
    }

    /** @return array{subject: string, title: string, eyebrow: string, preheader: string, intro: list<string>, outro: list<string>, actionText: string, actionUrl: string} */
    private function copy(): array
    {
        $tier = $this->context['tier'];
        $reference = $this->context['reference'];
        $trackUrl = route('membership.track', ['reference' => $reference]);

        return match ($this->stage) {
            'approved' => [
                'subject' => 'Your '.config('app.name').' membership is approved',
                'title' => 'Welcome, your membership is approved',
                'eyebrow' => 'Membership approved',
                'preheader' => "Your {$tier} card and certificate are ready.",
                'intro' => array_values(array_filter([
                    "We are pleased to confirm your {$tier} membership of ".config('app.name').'.',
                    'Your membership card and certificate of membership have been issued. Sign in to view and present them.',
                    isset($this->context['memberId']) ? 'Member ID: '.$this->context['memberId'] : null,
                    isset($this->context['serial']) ? 'Certificate serial: '.$this->context['serial'] : null,
                ])),
                'outro' => ['Anyone can confirm the authenticity of your credential by scanning the QR code on your card or certificate.'],
                'actionText' => 'View My Membership Card',
                'actionUrl' => route('account.show'),
            ],
            'declined' => [
                'subject' => 'An update on your '.config('app.name').' application',
                'title' => 'An update on your application',
                'eyebrow' => 'Application decision',
                'preheader' => 'We have completed our review of your application.',
                'intro' => [
                    "Thank you for applying for {$tier} membership. After careful review, we are unable to offer membership at this time.",
                    'This is not a reflection on your standing. Our tiers are extended to a deliberately small number of applicants.',
                ],
                'outro' => ['You are welcome to write to info@un-der.com if you would like to discuss the decision, and you may apply again in future.'],
                'actionText' => 'Review Your Application',
                'actionUrl' => $trackUrl,
            ],
            default => [
                'subject' => 'We have received your '.config('app.name').' membership application',
                'title' => 'Application received',
                'eyebrow' => 'Application received',
                'preheader' => "Your {$tier} application is with our review team.",
                'intro' => [
                    "Thank you for applying for {$tier} membership. A partner will review your application personally.",
                    "Your reference is {$reference}. Keep it safe: it lets you follow the progress of your application at any time.",
                ],
                'outro' => ['We review every application by hand, so timing varies. We will email you as soon as there is a decision.'],
                'actionText' => 'Track My Application',
                'actionUrl' => $trackUrl,
            ],
        };
    }
}
