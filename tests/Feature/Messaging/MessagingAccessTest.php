<?php

declare(strict_types=1);

namespace Tests\Feature\Messaging;

use App\Models\Connection;
use App\Models\Message;
use App\Models\User;
use App\Services\ConversationService;

final class MessagingAccessTest extends MessagingTestCase
{
    public function test_guests_are_sent_to_login(): void
    {
        [, , $c] = $this->connectedPair();

        $this->get(route('messages.index'))->assertRedirect(route('login'));
        $this->get(route('messages.show', $c))->assertRedirect(route('login'));
        $this->postJson(route('messages.store', $c), ['body' => 'hi'])->assertUnauthorized();
        $this->getJson(route('messages.poll', $c))->assertUnauthorized();
    }

    public function test_members_without_approval_are_redirected(): void
    {
        $plain = User::factory()->create();

        $this->actingAs($plain)->get(route('messages.index'))->assertRedirect(route('account.show'));
    }

    public function test_both_parties_can_open_the_thread_and_see_the_other_member(): void
    {
        [$a, $b, $c] = $this->connectedPair();

        $this->actingAs($a)->get(route('messages.show', $c))->assertOk()->assertSee('Bruno Bello')->assertSee('Principal Circle');
        $this->actingAs($b)->get(route('messages.show', $c))->assertOk()->assertSee('Alice Adeyemi');
    }

    public function test_outsiders_cannot_open_poll_send_or_report(): void
    {
        [$a, , $c] = $this->connectedPair();
        $outsider = $this->member('Outsider');
        $m = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $a->id]);

        $this->actingAs($outsider)->get(route('messages.show', $c))->assertNotFound();
        $this->actingAs($outsider)->getJson(route('messages.poll', $c))->assertNotFound();
        $this->actingAs($outsider)->postJson(route('messages.store', $c), ['body' => 'hi'])->assertNotFound();
        $this->actingAs($outsider)->postJson(route('messages.report', [$c, $m]))->assertNotFound();
    }

    public function test_pending_and_blocked_connections_are_closed(): void
    {
        foreach (['pending', 'blocked', 'declined'] as $status) {
            [$a, , $c] = $this->connectedPair($status);

            $this->actingAs($a)->get(route('messages.show', $c))->assertForbidden();
            $this->actingAs($a)->postJson(route('messages.store', $c), ['body' => 'hi'])->assertForbidden();
            $this->actingAs($a)->getJson(route('messages.poll', $c))->assertForbidden();
        }
    }

    public function test_blocking_after_the_fact_stops_sending_and_hides_the_conversation(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        $c->link->update(['status' => 'blocked']);

        $this->actingAs($b)->postJson(route('messages.store', $c), ['body' => 'hi'])->assertForbidden();
        $this->actingAs($a)->get(route('messages.index'))->assertOk()->assertDontSee('Bruno Bello');
    }

    public function test_inbox_lists_conversations_by_latest_activity_with_unread_badge(): void
    {
        [$a, $b, $first] = $this->connectedPair();
        $carol = $this->member('Carol Chidi');
        $second = app(ConversationService::class)->ensureFor(Connection::query()->create([
            'requester_id' => $carol->id, 'addressee_id' => $a->id, 'status' => 'accepted',
        ]));

        Message::factory()->create(['conversation_id' => $first->id, 'sender_id' => $b->id, 'body' => 'Older hello']);
        $first->update(['last_message_at' => now()->subDay()]);
        Message::factory()->create(['conversation_id' => $second->id, 'sender_id' => $carol->id, 'body' => 'Newest hello']);
        $second->update(['last_message_at' => now()]);

        $this->actingAs($a)->get(route('messages.index'))
            ->assertOk()
            ->assertSeeInOrder(['Carol Chidi', 'Newest hello', 'Bruno Bello', 'Older hello']);
    }

    public function test_empty_inbox_explains_how_messaging_opens(): void
    {
        $solo = $this->member('Solo');

        $this->actingAs($solo)->get(route('messages.index'))->assertOk()->assertSee('Messaging opens when a connection is accepted');
    }

    public function test_opening_a_thread_marks_received_messages_read(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        $m = Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $b->id]);

        $this->actingAs($a)->get(route('messages.show', $c))->assertOk();

        $this->assertNotNull($m->fresh()->read_at);
    }

    public function test_message_text_is_escaped_and_links_are_safe(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        Message::factory()->create(['conversation_id' => $c->id, 'sender_id' => $b->id, 'body' => "<script>alert(1)</script>\nsee https://example.com/x?a=1&b=2."]);

        $this->actingAs($a)->get(route('messages.show', $c))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false)
            ->assertSee('rel="noopener noreferrer nofollow ugc"', false)
            ->assertSee('href="https://example.com/x?a=1&amp;b=2"', false);
    }
}
