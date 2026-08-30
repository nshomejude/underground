<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Email verification, built on the core MustVerifyEmail contract: signed
 * links, the framework's VerifyEmail notification family (customised for
 * on-brand copy — see App\Notifications\VerifyEmailNotification), and
 * Illuminate\Foundation\Auth\EmailVerificationRequest for the handler.
 */
final class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_triggers_a_verification_notification(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Amara Diallo',
            'email' => 'amara@example.com',
            'password' => 'correct-horse-battery-staple',
            'password_confirmation' => 'correct-horse-battery-staple',
        ]);

        $user = User::query()->where('email', 'amara@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_verify_email_notice_renders_for_an_unverified_authenticated_user(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get(route('verification.notice'))->assertOk();
    }

    public function test_a_valid_signed_link_marks_the_email_as_verified(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $response = $this->actingAs($user)->get($url);

        $response->assertRedirect(route('account.show'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);

        Event::assertDispatched(Verified::class);
    }

    public function test_a_tampered_hash_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('someone-else@example.com')],
        );

        $this->actingAs($user)->get($url)->assertForbidden();

        $this->assertNull($user->refresh()->email_verified_at);
    }

    public function test_an_expired_link_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->subMinute(),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->actingAs($user)->get($url)->assertForbidden();

        $this->assertNull($user->refresh()->email_verified_at);
    }

    public function test_resend_sends_a_new_notification_for_an_unverified_user(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_resend_is_a_no_op_for_an_already_verified_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->actingAs($user)->post(route('verification.send'))
            ->assertRedirect(route('account.show'));

        Notification::assertNothingSent();
    }
}
