<?php

declare(strict_types=1);

namespace Tests\Feature\Messaging;

use App\Models\Connection;
use App\Models\Message;
use App\Services\ConversationService;
use App\Services\MessageService;

final class MessagingSendingTest extends MessagingTestCase
{
    public function test_sending_json_creates_the_message_and_updates_the_conversation(): void
    {
        [$a, , $c] = $this->connectedPair();

        $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => "  Hello there\nsecond line  "])
            ->assertCreated()
            ->assertJsonPath('message.mine', true)
            ->assertJsonPath('message.html', "Hello there<br>\nsecond line");

        $this->assertDatabaseHas('messages', ['conversation_id' => $c->id, 'sender_id' => $a->id, 'body' => "Hello there\nsecond line"]);
        $this->assertNotNull($c->fresh()->last_message_at);
    }

    public function test_plain_form_post_redirects_back_to_the_thread(): void
    {
        [$a, , $c] = $this->connectedPair();

        $this->actingAs($a)->post(route('messages.store', $c), ['body' => 'No JS here'])
            ->assertRedirect(route('messages.show', $c).'#composer');

        $this->assertDatabaseHas('messages', ['body' => 'No JS here']);
    }

    public function test_body_validation(): void
    {
        [$a, , $c] = $this->connectedPair();

        $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => '   '])->assertJsonValidationErrors('body');
        $this->actingAs($a)->postJson(route('messages.store', $c), [])->assertJsonValidationErrors('body');
        $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => str_repeat('a', 2001)])->assertJsonValidationErrors('body');
        $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => str_repeat('a', 2000)])->assertCreated();
    }

    public function test_identical_message_repeated_three_times_in_two_minutes_is_rejected(): void
    {
        [$a, , $c] = $this->connectedPair();

        $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => 'Buy now'])->assertCreated();
        $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => 'Buy now'])->assertCreated();
        $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => 'Buy now'])->assertJsonValidationErrors('body');

        $this->assertSame(2, Message::query()->where('body', 'Buy now')->count());
    }

    public function test_sending_is_throttled_to_thirty_per_minute(): void
    {
        [$a, , $c] = $this->connectedPair();

        for ($i = 0; $i < 30; $i++) {
            $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => "Message {$i}"])->assertCreated();
        }

        $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => 'One too many'])->assertStatus(429);
    }

    public function test_poll_returns_only_newer_messages_and_marks_received_ones_read(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        $old = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $b->id, 'body' => 'old']);
        $new = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $b->id, 'body' => 'new']);

        $this->actingAs($a)->getJson(route('messages.poll', [$c, 'after' => $old->id]))
            ->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonPath('messages.0.id', $new->id)
            ->assertJsonPath('unread', 0);

        $this->assertNotNull($old->fresh()->read_at);
        $this->assertNotNull($new->fresh()->read_at);
    }

    public function test_poll_reports_when_my_messages_were_seen(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        $mine = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id]);

        $this->actingAs($a)->getJson(route('messages.poll', [$c, 'after' => $mine->id]))->assertJsonPath('seen_id', 0);

        $this->actingAs($b)->getJson(route('messages.poll', $c))->assertOk();

        $this->actingAs($a)->getJson(route('messages.poll', [$c, 'after' => $mine->id]))->assertJsonPath('seen_id', $mine->id);
    }

    public function test_unread_counts_cover_only_open_conversations_and_received_messages(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        $carol = $this->member('Carol');
        $other = app(ConversationService::class)->ensureFor(Connection::query()->create([
            'requester_id' => $carol->id, 'addressee_id' => $a->id, 'status' => 'accepted',
        ]));

        Message::factory()->count(2)->create(['conversation_id' => $c->id, 'sender_id' => $b->id]);
        Message::factory()->create(['conversation_id' => $other->id, 'sender_id' => $carol->id]);
        Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id]);
        Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $b->id, 'read_at' => now()]);

        $this->assertSame(3, MessageService::unreadCount($a));
        $this->assertSame(1, MessageService::unreadCount($b));

        $other->link->update(['status' => 'blocked']);
        $this->assertSame(2, MessageService::unreadCount($a));

        $this->actingAs($a);
        $this->assertSame(2, MessageService::unreadCount());
        $this->assertStringContainsString('2', (string) $this->blade('<x-message-unread-badge />'));
    }

    public function test_the_unread_badge_renders_nothing_when_zero(): void
    {
        [$a] = $this->connectedPair();
        $this->actingAs($a);

        $this->assertSame('', trim((string) $this->blade('<x-message-unread-badge />')));
    }

    public function test_ensure_for_is_idempotent(): void
    {
        [, , $c] = $this->connectedPair();
        $service = app(ConversationService::class);

        $again = $service->ensureFor($c->link);

        $this->assertTrue($c->is($again));
        $this->assertSame(1, $c->link->conversation()->count());
    }

    public function test_reporting_a_message_stores_reason_and_reporter(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        $m = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $b->id]);

        $this->actingAs($a)->postJson(route('messages.report', [$c, $m]), ['reason' => 'Unsolicited offer'])->assertOk()->assertJsonPath('ok', true);

        $m->refresh();
        $this->assertNotNull($m->reported_at);
        $this->assertSame('Unsolicited offer', $m->report_reason);
        $this->assertSame($a->id, $m->reported_by);
    }

    public function test_report_rules(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        $theirs = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $b->id]);
        $mine = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id]);
        $elsewhere = Message::factory()->create();

        $this->actingAs($a)->postJson(route('messages.report', [$c, $mine]))->assertForbidden();
        $this->actingAs($a)->postJson(route('messages.report', [$c, $elsewhere]))->assertNotFound();
        $this->actingAs($a)->postJson(route('messages.report', [$c, $theirs]), ['reason' => str_repeat('x', 501)])->assertJsonValidationErrors('reason');

        $this->actingAs($a)->post(route('messages.report', [$c, $theirs]))
            ->assertRedirect(route('messages.show', $c))
            ->assertSessionHas('messaging_status');
    }
}
