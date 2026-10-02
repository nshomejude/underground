<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class OnboardingGapsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_links_to_password_reset_and_offers_remember_me(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee(route('password.request'), false)
            ->assertSee('name="remember"', false);
    }

    public function test_registration_requires_accepting_the_terms(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'correct-horse-battery-staple',
            'password_confirmation' => 'correct-horse-battery-staple',
        ])->assertSessionHasErrors('terms');

        $this->assertDatabaseMissing('users', ['email' => 'ada@example.com']);
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        User::factory()->create(['email' => 'member@example.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'member@example.com', 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }

        $this->post('/login', ['email' => 'member@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_changing_email_unverifies_it_and_sends_a_new_verification_link(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => now(), 'password' => Hash::make('secret-pass-123')]);

        $this->actingAs($user)->post('/account/settings', [
            'name' => $user->name,
            'email' => 'new-address@example.com',
            'current_password' => 'secret-pass-123',
        ])->assertRedirect(route('account.settings'));

        $user->refresh();
        $this->assertSame('new-address@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_changing_password_sends_a_security_notice(): void
    {
        Notification::fake();

        $user = User::factory()->create(['password' => Hash::make('old-password-123')]);

        $this->actingAs($user)->post('/account/settings/password', [
            'current_password' => 'old-password-123',
            'password' => 'brand-new-password-456',
            'password_confirmation' => 'brand-new-password-456',
        ])->assertRedirect(route('account.security'));

        Notification::assertSentTo($user, PasswordChangedNotification::class);
    }

    public function test_account_page_prompts_unverified_members_and_shows_checklist(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->get('/account')
            ->assertOk()
            ->assertSee('Please verify your email')
            ->assertSee('Getting started');
    }

    public function test_a_member_can_delete_their_own_account(): void
    {
        $user = User::factory()->create(['password' => Hash::make('secret-pass-123')]);

        $this->actingAs($user)->delete('/account', ['current_password' => 'secret-pass-123'])
            ->assertRedirect(route('home'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertGuest();
    }

    public function test_deleting_requires_the_correct_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('secret-pass-123')]);

        $this->actingAs($user)->from('/account/settings')->delete('/account', ['current_password' => 'wrong'])
            ->assertSessionHasErrors('current_password', null, 'deleteAccount');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_verification_email_renders_with_the_branded_template(): void
    {
        $user = User::factory()->create();
        $mail = (new VerifyEmailNotification)->toMail($user);

        $html = (string) view($mail->view[0], $mail->viewData)->render();

        $this->assertStringContainsString('Confirm your email address', $html);
        $this->assertStringContainsString('opesware.com', $html);
    }
}
