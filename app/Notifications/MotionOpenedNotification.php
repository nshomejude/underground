<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Motion;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** A motion you may vote on has opened. */
final class MotionOpenedNotification extends Notification
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
            subject: 'A vote is open: '.$m->title,
            heading: 'A motion is open for',
            headingEm: 'your vote.',
            intro: array_values(array_filter([
                $m->summary ?: $m->title,
                'Voting closes on '.$m->closes_at->format('j F Y \a\t H:i').'.',
            ])),
            actionText: 'Read and Vote',
            actionUrl: route('votes.show', $m),
            outro: ['Votes are cast inside your member dashboard only. Your voice is one vote.'],
            eyebrow: 'Member vote',
            preheader: $m->title,
            summary: [
                ['Motion', $m->title],
                ['Type', $m->kindLabel()],
                ['Closes', $m->closes_at->format('j M Y, H:i')],
            ],
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'stage' => 'motion_opened',
            'title' => 'Vote open: '.$this->motion->title,
            'body' => 'Closes '.$this->motion->closes_at->format('j M Y, H:i').'.',
            'url' => route('votes.show', $this->motion),
            'motion_id' => $this->motion->id,
        ];
    }
}
