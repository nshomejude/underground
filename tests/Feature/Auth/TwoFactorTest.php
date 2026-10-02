<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\TwoFactorChangedNotification;
use App\Support\Totp;
use App\Support\TwoFactor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $attrs
     * @return array{0: User, 1: string}
     */
    private function enrolled(array $attrs = []): array
    {
        $user = User::factory()->create($attrs);
        TwoFactor::beginEnrolment($user);
        $secret = (string) $user->fresh()->twoFactorSecret();
        TwoFactor::confirm($user->fresh());

        return [$user->fresh(), $secret];
    }

    private function nextCode(string $secret): string
    {
        return Totp::code($secret, now()->getTimestamp());
    }

    public function test_enrolment_confirm_success_shows_codes_once_and_notifies(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('account.two-factor.start'), ['current_password' => 'password'])->assertRedirect(route('account.security'));
        $user = $user->fresh();
        $this->assertTrue($user->hasPendingTwoFactorSetup());
        $this->assertNotSame($user->twoFactorSecret(), $user->getRawOriginal('two_factor_secret'), 'secret stored encrypted');

        $this->actingAs($user)->get(route('account.security'))->assertOk()->assertSee('<svg', false)->assertSee('Confirm and enable');

        $this->actingAs($user)->post(route('account.two-factor.confirm'), ['code' => $this->nextCode($user->twoFactorSecret())])
            ->assertRedirect(route('account.security'));

        $user = $user->fresh();
        $this->assertTrue($user->hasTwoFactorEnabled());
        $this->assertSame(8, $user->recoveryCodesRemaining());
        Notification::assertSentTo($user, TwoFactorChangedNotification::class, fn ($n) => $n->event === 'enabled');

        $this->actingAs($user)->get(route('account.security'))->assertOk()->assertSee('Save your recovery codes now');
        $this->actingAs($user)->post(route('account.two-factor.acknowledge'), [])->assertSessionHasErrors('saved');
        $this->actingAs($user)->post(route('account.two-factor.acknowledge'), ['saved' => '1'])->assertSessionHasNoErrors();
        $this->actingAs($user)->get(route('account.security'))->assertDontSee('Save your recovery codes now');
        $this->assertArrayNotHasKey('two_factor_secret', $user->toArray());
    }

    public function test_start_requires_password(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('account.two-factor.start'), ['current_password' => 'wrong'])
            ->assertSessionHasErrorsIn('twoFactorStart', 'current_password');
        $this->assertNull($user->fresh()->two_factor_secret);
    }

    public function test_wrong_code_does_not_enable(): void
    {
        $user = User::factory()->create();
        TwoFactor::beginEnrolment($user);

        $this->actingAs($user->fresh())->post(route('account.two-factor.confirm'), ['code' => '000000'])
            ->assertSessionHasErrorsIn('twoFactorConfirm', 'code');
        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_code_replay_rejected(): void
    {
        [$user, $secret] = $this->enrolled();
        $code = $this->nextCode($secret);

        $this->assertTrue(TwoFactor::verifyCode($user, $code));
        $this->assertFalse(TwoFactor::verifyCode($user->fresh(), $code));
        $this->assertFalse(TwoFactor::verifyCode(User::findOrFail($user->id), $code));
    }

    public function test_login_redirects_to_challenge_without_authenticating(): void
    {
        [$user] = $this->enrolled();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();
        $this->get(route('two-factor.challenge'))->assertOk()->assertSee('recovery code', false);
        $this->get(route('account.show'))->assertRedirect('/login');
    }

    public function test_challenge_without_pending_state_redirects_to_login(): void
    {
        $this->get(route('two-factor.challenge'))->assertRedirect(route('login'));
        $this->post(route('two-factor.login'), ['code' => '123456'])->assertRedirect(route('login'));
    }

    public function test_correct_totp_logs_in(): void
    {
        [$user, $secret] = $this->enrolled();
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->post(route('two-factor.login'), ['code' => $this->nextCode($secret)])->assertRedirect(route('account.show'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_code_does_not_log_in(): void
    {
        [$user] = $this->enrolled();
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->post(route('two-factor.login'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_pending_state_expires_after_ten_minutes(): void
    {
        [$user, $secret] = $this->enrolled();
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->travel(11)->minutes();
        $this->post(route('two-factor.login'), ['code' => $this->nextCode($secret)])->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_recovery_code_works_once_only(): void
    {
        Notification::fake();
        [$user] = $this->enrolled();
        $codes = TwoFactor::regenerateRecoveryCodes($user);

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->post(route('two-factor.login'), ['recovery_code' => strtoupper($codes[0])])->assertRedirect(route('account.show'));
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, TwoFactorChangedNotification::class, fn ($n) => $n->event === 'recovery_used');
        $this->assertSame(7, $user->fresh()->recoveryCodesRemaining());

        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->post(route('two-factor.login'), ['recovery_code' => $codes[0]])->assertSessionHasErrors('recovery_code');
        $this->assertGuest();
        $this->post(route('two-factor.login'), ['recovery_code' => $codes[1]])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_lockout_after_five_failures(): void
    {
        [$user, $secret] = $this->enrolled();
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('two-factor.login'), ['code' => '000000'])->assertSessionHasErrors('code');
        }

        $this->post(route('two-factor.login'), ['code' => $this->nextCode($secret)])->assertSessionHasErrors('code');
        $this->assertGuest();
        $this->assertStringContainsString('Too many attempts', session('errors')->first('code'));
    }

    public function test_admin_without_2fa_is_redirected_from_admin_when_enforced(): void
    {
        config(['auth.require_admin_two_factor' => true]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('account.security'));
        $this->actingAs($admin)->get(route('account.security'))->assertOk();

        [$enrolledAdmin] = $this->enrolled(['is_admin' => true]);
        $this->actingAs($enrolledAdmin)->get('/admin')->assertOk();
    }

    public function test_admin_enforcement_off_by_default_in_tests(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_enforced_admin_cannot_disable(): void
    {
        config(['auth.require_admin_two_factor' => true]);
        [$admin, $secret] = $this->enrolled(['is_admin' => true]);

        $this->actingAs($admin)->post(route('account.two-factor.disable'), ['current_password' => 'password', 'code' => $this->nextCode($secret)])
            ->assertSessionHasErrorsIn('twoFactorDisable', 'code');
        $this->assertTrue($admin->fresh()->hasTwoFactorEnabled());
    }

    public function test_disable_requires_password_and_code_and_notifies(): void
    {
        Notification::fake();
        [$user, $secret] = $this->enrolled();

        $this->actingAs($user)->post(route('account.two-factor.disable'), ['current_password' => 'nope', 'code' => $this->nextCode($secret)])
            ->assertSessionHasErrorsIn('twoFactorDisable', 'current_password');
        $this->actingAs($user)->post(route('account.two-factor.disable'), ['current_password' => 'password', 'code' => '000000'])
            ->assertSessionHasErrorsIn('twoFactorDisable', 'code');
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());

        $this->actingAs($user)->post(route('account.two-factor.disable'), ['current_password' => 'password', 'code' => $this->nextCode($secret)])
            ->assertSessionHasNoErrors();
        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
        $this->assertNull($user->fresh()->two_factor_secret);
        Notification::assertSentTo($user, TwoFactorChangedNotification::class, fn ($n) => $n->event === 'disabled');
    }

    public function test_regenerate_requires_password_invalidates_old_codes_and_notifies(): void
    {
        Notification::fake();
        [$user] = $this->enrolled();
        $old = TwoFactor::regenerateRecoveryCodes($user);

        $this->actingAs($user)->post(route('account.two-factor.regenerate'), ['current_password' => 'bad'])
            ->assertSessionHasErrorsIn('twoFactorRegenerate', 'current_password');

        $this->actingAs($user)->post(route('account.two-factor.regenerate'), ['current_password' => 'password'])->assertSessionHasNoErrors();
        $this->assertFalse(TwoFactor::consumeRecoveryCode($user->fresh(), $old[0]));
        Notification::assertSentTo($user, TwoFactorChangedNotification::class, fn ($n) => $n->event === 'regenerated');
    }

    public function test_password_change_and_account_deletion_still_work(): void
    {
        [$user] = $this->enrolled();

        $this->actingAs($user)->post(route('account.settings.password'), [
            'current_password' => 'password', 'password' => 'N3w-Passw0rd-xyz!', 'password_confirmation' => 'N3w-Passw0rd-xyz!',
        ])->assertSessionHasNoErrors();

        $this->actingAs($user->fresh())->delete(route('account.destroy'), ['current_password' => 'N3w-Passw0rd-xyz!'])->assertRedirect(route('home'));
        $this->assertNull(User::find($user->id));
    }
}
