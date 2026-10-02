<?php

declare(strict_types=1);

namespace Tests\Feature\Voting;

use App\Models\IdentityVerification;
use App\Models\Motion;
use App\Models\User;
use Tests\Feature\Messaging\MessagingTestCase;

final class MotionPagesTest extends MessagingTestCase
{
    private function verified(string $tier = 'sovereign-partner'): User
    {
        $u = $this->member('Voter', $tier);
        $u->forceFill(['email_verified_at' => now()])->save();
        IdentityVerification::factory()->approved()->create(['user_id' => $u->id]);

        return $u;
    }

    private function payload(array $over = []): array
    {
        return $over + [
            'intent' => 'publish', 'title' => 'Adopt the new charter', 'summary' => 'Short', 'body' => 'Long text',
            'kind' => 'decision', 'min_tier_rank' => 2, 'quorum' => 3, 'pass_threshold' => 60,
            'show_results' => 'after_close', 'closes_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
        ];
    }

    public function test_member_create_form_renders_for_eligible_member(): void
    {
        $this->actingAs($this->verified())->get(route('votes.create'))
            ->assertOk()->assertSee('Principal Circle and above');
    }

    public function test_member_create_form_forbidden_for_low_tier(): void
    {
        $this->actingAs($this->verified('corporate-affiliate'))->get(route('votes.create'))->assertForbidden();
    }

    public function test_member_can_create_a_motion(): void
    {
        $user = $this->verified();
        $this->actingAs($user)->post(route('votes.store'), $this->payload())->assertRedirect();
        $this->assertDatabaseHas('motions', ['title' => 'Adopt the new charter', 'created_by' => $user->id, 'status' => Motion::OPEN]);
    }

    public function test_member_can_save_a_draft_and_edit_it(): void
    {
        $user = $this->verified();
        $this->actingAs($user)->post(route('votes.store'), $this->payload(['intent' => 'draft']))->assertRedirect();
        $motion = Motion::query()->firstOrFail();
        $this->assertTrue($motion->isDraft());
        $this->actingAs($user)->get(route('votes.edit', $motion))->assertOk()->assertSee('Adopt the new charter');
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $motion = Motion::factory()->create();

        $this->actingAs($admin)->get(route('admin.motions.index'))->assertOk()->assertSee($motion->title);
        $this->actingAs($admin)->get(route('admin.motions.index', ['status' => 'open']))->assertOk();
        $this->actingAs($admin)->get(route('admin.motions.create'))->assertOk()->assertSee('Principal Circle and above');
        $this->actingAs($admin)->get(route('admin.motions.show', $motion))->assertOk()->assertSee($motion->title);
        $this->actingAs($admin)->get(route('admin.motions.export'))->assertOk();
    }

    public function test_admin_can_create_close_and_view_decided_motion(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.motions.store'), $this->payload())->assertRedirect();
        $motion = Motion::query()->firstOrFail();

        $this->actingAs($admin)->post(route('admin.motions.close', $motion), ['reason' => 'Done early'])->assertRedirect();
        $this->actingAs($admin)->get(route('admin.motions.show', $motion))->assertOk();
    }

    public function test_non_admin_cannot_reach_admin_motions(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.motions.index'))->assertForbidden();
    }
}
