<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\PlanChangeRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Tells staff (via the configured mail address) that a member asked to change plan. */
final class PlanRequestStaffNotification extends Notification
{
    public function __construct(public readonly PlanChangeRequest $request) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $member = $this->request->user;

        return BrandedMessage::make(
            subject: 'Plan request from '.$member->name,
            heading: 'New plan request',
            intro: [
                "{$member->name} ({$member->email}) asked to move to the {$this->request->toPlan->name} plan.",
                $this->request->note ? 'Their note: '.$this->request->note : 'They did not add a note.',
            ],
            actionText: 'Review the request',
            actionUrl: route('admin.plan-requests.show', $this->request),
            eyebrow: 'Staff notice',
            preheader: "{$member->name} requested {$this->request->toPlan->name}.",
            summary: array_values(array_filter([
                ['Member', $member->name],
                ['Requested plan', $this->request->toPlan->name],
                $this->request->fromPlan ? ['Current plan', $this->request->fromPlan->name] : null,
            ])),
        );
    }
}
