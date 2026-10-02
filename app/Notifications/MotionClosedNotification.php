<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Motion;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** The outcome of a motion, sent to its voters and its creator. */
final class MotionClosedNotification extends Notification
{
    public const LABELS = [
        'passed' => 'Passed',
        'rejected' => 'Rejected',
        'no_quorum' => 'No quorum',
        'decided' => 'Decided',
        'tied' => 'Tied',
    ];

    public function __construct(public readonly Motion $motion) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $m = $this->motion;
        $label = self::LABELS[$m->outcome] ?? 'Closed';

        return BrandedMessage::make(
            subject: 'Result: '.$m->title.' ('.$label.')',
            heading: 'The vote has closed:',
            headingEm: $label.'.',
            intro: [
                '"'.$m->title.'" has closed.',
                (string) ($m->result['statement'] ?? ''),
            ],
            actionText: 'View the Result',
            actionUrl: route('votes.show', $m),
            eyebrow: 'Vote result',
            preheader: $label.': '.$m->title,
            summary: [
                ['Outcome', $label],
                ['Members voting', (string) ($m->result['voters'] ?? 0)],
            ],
        );
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $label = self::LABELS[$this->motion->outcome] ?? 'Closed';

        return [
            'stage' => 'motion_closed',
            'title' => 'Result: '.$this->motion->title,
            'body' => $label,
            'url' => route('votes.show', $this->motion),
            'motion_id' => $this->motion->id,
        ];
    }
}
