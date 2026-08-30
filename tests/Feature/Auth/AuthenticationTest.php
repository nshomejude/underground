<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Standard Laravel session-based authentication, hand-rolled on core
 * framework primitives only (Auth/Hash facades, session) — no
 * Breeze/Fortify/Jetstream.
 */
final class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_register_and_is_logged_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Amara Diallo',
            'email' => 'amara@example.com',
            'password' => 'correct-horse-battery-staple',
            'password_confirmation' => 'correct-horse-battery-staple',
        ]);

        $response->assertRedirect(route('account.show'));

        $this->assertAuthenticated();

        $user = User::query()->where('email', 'amara@example.com')->firstOrFail();
        $this->assertSame('Amara Diallo', $user->name);
        $this->assertTrue(Hash::check('correct-horse-battery-staple', $user->password));
    }

    public function test_registration_rejects_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Amara Diallo',
            'email' => 'taken@example.com',
            'password' => 'correct-horse-battery-staple',
            'password_confirmation' => 'correct-horse-battery-staple',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(1, User::query()->where('email', 'taken@example.com')->count());
    }

    public function test_a_member_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'jordan@example.com',
            'password' => Hash::make('the-right-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jordan@example.com',
            'password' => 'the-right-password',
        ]);

        $response->assertRedirect(route('account.show'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_an_incorrect_password(): void
    {
        User::factory()->create([
            'email' => 'jordan@example.com',
            'password' => Hash::make('the-right-password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'jordan@example.com',
            'password' => 'the-wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_logged_in_member_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_account_redirects_guests_to_login(): void
    {
        $this->get(route('account.show'))->assertRedirect(route('login'));
    }
}
