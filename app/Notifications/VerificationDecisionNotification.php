<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Milestones of an identity or company verification:
 * submitted, approved, rejected, info_requested. Email + dashboard item.
 */
final class VerificationDecisionNotification extends Notification
{
    /** @param  array<string, string>  $context */
    public function __construct(public readonly string $kind, public readonly string $stage, public readonly array $context = []) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $c = $this->copy();

        return BrandedMessage::make(
            subject: $c['subject'],
            heading: $c['title'],
            intro: $c['intro'],
            actionText: $c['actionText'],
            actionUrl: $c['url'],
            outro: $c['outro'],
            eyebrow: $c['eyebrow'],
            preheader: $c['body'],
            summary: $c['summary'] ?? [],
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $c = $this->copy();

        return ['stage' => 'verification_'.$this->kind.'_'.$this->stage, 'title' => $c['title'], 'body' => $c['body'], 'url' => $c['url']];
    }

    /** @return array<string, mixed> */
    private function copy(): array
    {
        $what = $this->kind === 'company' ? 'company verification' : 'identity verification';
        $url = route('verification.index');
        $window = (string) config('verification.review_window');

        return match ($this->stage) {
            'approved' => [
                'subject' => 'Your '.$what.' is approved',
                'title' => ucfirst($what).' approved',
                'eyebrow' => 'Verification',
                'body' => 'Your '.$what.' has been approved.',
                'intro' => ['Good news: our team has approved your '.$what.'.', $this->kind === 'company'
                    ? 'Your profile now carries the Verified company badge.'
                    : 'You can now appear in the member directory, connect with other members and take part in votes, subject to your tier.'],
                'summary' => isset($this->context['expires']) ? [['Valid until', $this->context['expires']]] : [],
                'outro' => ['We will remind you before it needs renewing.'],
                'actionText' => 'View Verification',
                'url' => $url,
            ],
            'rejected' => [
                'subject' => 'An update on your '.$what,
                'title' => 'We could not approve your '.$what,
                'eyebrow' => 'Verification',
                'body' => 'Your '.$what.' needs attention.',
                'intro' => ['After review we were unable to approve your '.$what.'.', 'Reason: '.($this->context['reason'] ?? 'see your verification page').'.'],
                'summary' => [],
                'outro' => ['You can start again with corrected details or clearer documents whenever you are ready.'],
                'actionText' => 'Try Again',
                'url' => $url,
            ],
            'info_requested' => [
                'subject' => 'We need a little more for your '.$what,
                'title' => 'More information needed',
                'eyebrow' => 'Verification',
                'body' => 'Our reviewer has asked a question about your '.$what.'.',
                'intro' => ['Our reviewer has a question about your '.$what.':', '"'.($this->context['message'] ?? '').'"'],
                'summary' => [],
                'outro' => ['Reply to this email or write to info@un-der.com and we will pick the review back up.'],
                'actionText' => 'View Verification',
                'url' => $url,
            ],
            default => [
                'subject' => 'We received your '.$what,
                'title' => 'Your '.$what.' is with our team',
                'eyebrow' => 'Verification',
                'body' => 'Your documents are in the review queue.',
                'intro' => ['Thank you. Your documents are securely stored and in the queue for manual review.', 'Our team reviews submissions within '.$window.'. We will email you when there is a decision.'],
                'summary' => [['Review window', $window]],
                'outro' => ['Only authorised reviewers can see your documents. You can withdraw your submission and delete your files at any time.'],
                'actionText' => 'Check Status',
                'url' => $url,
            ],
        };
    }
}
