<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Forgot-password / reset-password, built on the core Password broker
 * (Illuminate\Auth\Passwords\PasswordBroker) and a custom on-brand
 * ResetPasswordNotification (see App\Notifications).
 */
final class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_form_renders(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_a_valid_email_sends_the_reset_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'jordan@example.com']);

        $response = $this->post(route('password.email'), ['email' => 'jordan@example.com']);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_an_unknown_email_shows_the_same_generic_message_without_sending_anything(): void
    {
        Notification::fake();

        $knownResponse = $this->from('/forgot-password')->post(route('password.email'), ['email' => 'unknown@example.com']);

        $genericStatus = $knownResponse->getSession()->get('status');
        $this->assertNotEmpty($genericStatus);

        Notification::assertNothingSent();

        // Confirm a known email produces the identical confirmation message,
        // so the response never reveals which addresses are registered.
        User::factory()->create(['email' => 'jordan@example.com']);

        $secondResponse = $this->post(route('password.email'), ['email' => 'jordan@example.com']);

        $this->assertSame($genericStatus, $secondResponse->getSession()->get('status'));
    }

    public function test_reset_password_form_renders(): void
    {
        $user = User::factory()->create(['email' => 'jordan@example.com']);

        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk();
    }

    public function test_a_valid_token_resets_the_password_and_old_credentials_stop_working(): void
    {
        $user = User::factory()->create([
            'email' => 'jordan@example.com',
            'password' => Hash::make('old-password-123'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'jordan@example.com',
            'password' => 'brand-new-password-456',
            'password_confirmation' => 'brand-new-password-456',
        ]);

        $response->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('brand-new-password-456', $user->password));
        $this->assertFalse(Hash::check('old-password-123', $user->password));

        // Old credentials no longer authenticate.
        $this->post('/login', [
            'email' => 'jordan@example.com',
            'password' => 'old-password-123',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        // New credentials do.
        $this->post('/login', [
            'email' => 'jordan@example.com',
            'password' => 'brand-new-password-456',
        ]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_an_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create(['email' => 'jordan@example.com']);

        $response = $this->from('/reset-password/bogus-token')->post(route('password.update'), [
            'token' => 'bogus-token',
            'email' => 'jordan@example.com',
            'password' => 'brand-new-password-456',
            'password_confirmation' => 'brand-new-password-456',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
