<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\PlanChangeRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Emails the member the outcome of their plan request. */
final class PlanRequestDecidedNotification extends Notification
{
    public function __construct(public readonly PlanChangeRequest $request) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->request->toPlan->name;
        $approved = $this->request->status === 'approved';

        $intro = $approved
            ? ["Good news: your request to move to the {$plan} plan has been approved.", 'Our team will complete the change on your membership and write to you once it is done.']
            : ["Thank you for your interest in the {$plan} plan. We are not able to move your membership at this time."];

        if ($this->request->response_note) {
            $intro[] = 'A note from our team: '.$this->request->response_note;
        }

        return BrandedMessage::make(
            subject: $approved ? 'Your plan request is approved' : 'An update on your plan request',
            heading: $approved ? 'Plan request approved' : 'An update on your plan request',
            intro: $intro,
            actionText: 'View My Plans',
            actionUrl: route('plans.index'),
            outro: ['Write to '.config('mail.from.address').' if you would like to talk it through.'],
            eyebrow: 'Plan request',
            preheader: $approved ? "Your move to {$plan} is approved." : 'We have reviewed your plan request.',
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->request->status === 'approved' ? 'Plan request approved' : 'Plan request update',
            'body' => 'Your request for the '.$this->request->toPlan->name.' plan was '.$this->request->status.'.',
            'url' => route('plans.requests'),
        ];
    }
}
