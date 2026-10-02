<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Motion;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Sent once, about 24 hours before close, to eligible members who have not voted. */
final class MotionReminderNotification extends Notification
{
    public function __construct(public readonly Motion $motion) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $m = $this->motion;

        return BrandedMessage::make(
            subject: 'Voting closes soon: '.$m->title,
            heading: 'Voting closes',
            headingEm: 'within a day.',
            intro: [
                'You have not yet voted on "'.$m->title.'".',
                'Voting closes on '.$m->closes_at->format('j F Y \a\t H:i').'.',
            ],
            actionText: 'Cast My Vote',
            actionUrl: route('votes.show', $m),
            eyebrow: 'Closing soon',
            preheader: 'Your vote is still needed.',
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'stage' => 'motion_closing_soon',
            'title' => 'Closing soon: '.$this->motion->title,
            'body' => 'You have not voted yet.',
            'url' => route('votes.show', $this->motion),
            'motion_id' => $this->motion->id,
        ];
    }
}
