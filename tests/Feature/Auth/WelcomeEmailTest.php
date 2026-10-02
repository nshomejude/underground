<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class WelcomeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_registering_sends_welcome_email_from_welcome_address(): void
    {
        Mail::fake();

        $this->post('/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'a-strong-passphrase-1',
            'password_confirmation' => 'a-strong-passphrase-1',
            'terms' => '1',
        ])->assertRedirect();

        Mail::assertSent(WelcomeMail::class, function (WelcomeMail $mail): bool {
            return $mail->hasTo('ada@example.com') && $mail->envelope()->from->address === 'welcome@un-der.com';
        });
    }

    public function test_welcome_email_renders_html_and_text(): void
    {
        $user = User::factory()->make(['name' => 'Ada Lovelace']);
        $mail = new WelcomeMail($user);

        $mail->assertSeeInHtml('Ada, you');
        $mail->assertSeeInText('WELCOME, ADA');
    }
}
