<?php

declare(strict_types=1);

namespace Tests\Feature\Network;

use App\Models\Connection;
use App\Models\MemberProfile;
use App\Models\User;
use Tests\Feature\Messaging\MessagingTestCase;

final class ConnectionViewsTest extends MessagingTestCase
{
    public function test_hub_renders_each_tab_with_empty_states(): void
    {
        $a = $this->member('Alice Adeyemi');

        foreach (['connections', 'received', 'sent', 'blocked'] as $tab) {
            $this->actingAs($a)->get(route('network.connections', ['tab' => $tab]))->assertOk()->assertSee('Browse the directory');
        }
    }

    public function test_hub_lists_connected_member_with_message_button(): void
    {
        [$a, $b, $c] = $this->connectedPair();
        MemberProfile::factory()->create(['user_id' => $b->id, 'display_name' => 'Bruno Bello']);

        $this->actingAs($a)->get(route('network.connections'))
            ->assertOk()->assertSee('Bruno Bello')->assertSee(route('messages.show', $c), false);
    }

    public function test_received_request_can_be_declined_and_unverified_accept_is_refused(): void
    {
        $a = $this->member('Alice');
        $b = $this->member('Bruno');
        $accept = Connection::factory()->between($a, $b)->create();
        $decline = Connection::factory()->between($this->member('Cara'), $b)->create();

        $this->actingAs($b)->get(route('network.connections', ['tab' => 'received']))->assertOk()->assertSee('Accept')->assertSee('Decline');

        $this->actingAs($b)->post(route('network.respond', $accept), ['action' => 'accept'])
            ->assertSessionHas('network_error');
        $this->actingAs($b)->post(route('network.respond', $decline), ['action' => 'decline'])->assertRedirect();

        $this->assertSame('pending', $accept->fresh()->status);
        $this->assertSame('declined', $decline->fresh()->status);
    }

    public function test_sent_request_can_be_withdrawn_and_connection_removed(): void
    {
        $a = $this->member('Alice');
        $b = $this->member('Bruno');
        $pending = Connection::factory()->between($a, $b)->create();

        $this->actingAs($a)->get(route('network.connections', ['tab' => 'sent']))->assertOk()->assertSee('Withdraw');
        $this->actingAs($a)->post(route('network.withdraw', $pending))->assertRedirect();
        $this->assertNotSame('pending', $pending->fresh()?->status);

        $accepted = Connection::factory()->between($a, $this->member('Cara'))->accepted()->create();
        $this->actingAs($a)->post(route('network.remove', $accepted))->assertRedirect(route('network.connections'));
    }

    public function test_blocked_tab_shows_unblock(): void
    {
        $a = $this->member('Alice');
        $b = $this->member('Bruno');
        MemberProfile::factory()->create(['user_id' => $b->id]);
        Connection::factory()->between($a, $b)->blockedBy($a)->create();

        $this->actingAs($a)->get(route('network.connections', ['tab' => 'blocked']))->assertOk()->assertSee('Unblock');
    }

    public function test_admin_can_browse_and_view_connections_but_members_cannot(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $c = Connection::factory()->collaboration()->create();

        $this->actingAs($admin)->get(route('admin.network.index'))->assertOk()->assertSee('Collaborations');
        $this->actingAs($admin)->get(route('admin.network.index', ['status' => 'pending']))->assertOk();
        $this->actingAs($admin)->get(route('admin.network.show', $c))->assertOk()->assertSee('Joint venture exploration');

        $member = User::factory()->create(['is_admin' => false]);
        $this->actingAs($member)->get(route('admin.network.index'))->assertForbidden();
    }
}
