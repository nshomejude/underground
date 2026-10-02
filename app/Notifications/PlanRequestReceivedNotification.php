<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\PlanChangeRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Receipt emailed to a member who asked to move to another plan. */
final class PlanRequestReceivedNotification extends Notification
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

        return BrandedMessage::make(
            subject: 'We received your plan request',
            heading: 'Plan request received',
            intro: [
                "Thank you. We have received your request to move to the {$plan} plan.",
                'A partner will review it personally and write to you with the outcome. Nothing changes on your membership until then.',
            ],
            actionText: 'View My Requests',
            actionUrl: route('plans.requests'),
            outro: ['Questions in the meantime? Write to '.config('mail.from.address').'.'],
            eyebrow: 'Plan request',
            preheader: "Your request for {$plan} is with our team.",
            summary: array_values(array_filter([
                ['Requested plan', $plan],
                $this->request->fromPlan ? ['Current plan', $this->request->fromPlan->name] : null,
            ])),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Plan request received',
            'body' => 'Your request for the '.$this->request->toPlan->name.' plan is with our team.',
            'url' => route('plans.requests'),
        ];
    }
}
