<?php

declare(strict_types=1);

namespace Tests\Feature\Messaging;

use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Notification;

final class NotifyUnreadMessagesTest extends MessagingTestCase
{
    public function test_unread_messages_older_than_ten_minutes_notify_the_recipient_once_per_hour(): void
    {
        Notification::fake();
        [$a, $b, $c] = $this->connectedPair();

        $this->travelTo(now()->subMinutes(30));
        $first = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id, 'body' => 'secret body one']);
        Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id, 'body' => 'secret body two']);
        $this->travelBack();

        $this->artisan('messages:notify-unread')->assertSuccessful();
        Notification::assertSentToTimes($b, NewMessageNotification::class, 1);
        Notification::assertNotSentTo($a, NewMessageNotification::class);
        $this->assertNotNull($first->fresh()->notified_at);

        // a newer unread message within the hour does not trigger another email
        $this->travelTo(now()->subMinutes(15));
        Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id, 'body' => 'secret body three']);
        $this->travelBack();

        $this->artisan('messages:notify-unread')->assertSuccessful();
        Notification::assertSentToTimes($b, NewMessageNotification::class, 1);

        // after the hour it may notify again
        $this->travel(2)->hours();
        $this->artisan('messages:notify-unread')->assertSuccessful();
        Notification::assertSentToTimes($b, NewMessageNotification::class, 2);
    }

    public function test_recent_read_or_closed_messages_are_ignored(): void
    {
        Notification::fake();
        [$a, $b, $c] = $this->connectedPair();

        Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id]);
        $this->travelTo(now()->subHour());
        Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id, 'read_at' => now()]);
        $this->travelBack();

        $this->artisan('messages:notify-unread')->assertSuccessful();
        Notification::assertNothingSent();

        $this->travelTo(now()->subHour());
        Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id]);
        $this->travelBack();
        $c->link->update(['status' => 'blocked']);

        $this->artisan('messages:notify-unread')->assertSuccessful();
        Notification::assertNothingSent();
    }

    public function test_the_email_never_contains_the_message_body(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        $message = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id, 'body' => 'TOPSECRETBODY']);
        $notification = new NewMessageNotification('Alice Adeyemi', route('messages.show', $c));

        $html = (string) $notification->toMail($b)->render();

        $this->assertStringContainsString('You have a new message from Alice Adeyemi', $html);
        $this->assertStringContainsString(route('messages.show', $c), $html);
        $this->assertStringNotContainsString('TOPSECRETBODY', $html);
        $this->assertStringNotContainsString('TOPSECRETBODY', json_encode($notification->toArray($b)));
        $this->assertNotNull($message);
    }
}
