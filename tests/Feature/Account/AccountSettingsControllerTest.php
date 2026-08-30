<?php

declare(strict_types=1);

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AccountSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_requires_auth(): void
    {
        $this->get(route('account.settings'))->assertRedirect(route('login'));
    }

    public function test_settings_page_shows_the_current_name_and_email(): void
    {
        $user = User::factory()->create(['name' => 'Isabelle Fontaine', 'email' => 'isabelle@example.com']);

        $this->actingAs($user)->get(route('account.settings'))
            ->assertOk()
            ->assertSee('Isabelle Fontaine')
            ->assertSee('isabelle@example.com');
    }

    public function test_name_can_be_updated_without_touching_email(): void
    {
        $user = User::factory()->create(['name' => 'Old Name', 'email' => 'unchanged@example.com']);

        $response = $this->actingAs($user)->post(route('account.settings.update'), [
            'name' => 'New Name',
            'email' => 'unchanged@example.com',
        ]);

        $response->assertRedirect(route('account.settings'));
        $response->assertSessionHas('status');
        $response->assertSessionDoesntHaveErrors();

        $this->assertSame('New Name', $user->fresh()->name);
        $this->assertSame('unchanged@example.com', $user->fresh()->email);
    }

    public function test_email_can_be_updated_with_the_correct_current_password(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->actingAs($user)->post(route('account.settings.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            'current_password' => 'correct-password',
        ]);

        $response->assertRedirect(route('account.settings'));
        $response->assertSessionDoesntHaveErrors();

        $this->assertSame('new@example.com', $user->fresh()->email);
    }

    public function test_email_change_is_rejected_without_the_correct_current_password(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->actingAs($user)->post(route('account.settings.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
            'current_password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('current_password', null, 'updateProfile');

        $this->assertSame('old@example.com', $user->fresh()->email);
    }

    public function test_email_change_is_rejected_with_no_current_password_at_all(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->actingAs($user)->post(route('account.settings.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

        $response->assertSessionHasErrors('current_password', null, 'updateProfile');

        $this->assertSame('old@example.com', $user->fresh()->email);
    }

    public function test_password_can_be_changed_and_the_new_password_works_afterward(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->post(route('account.settings.password'), [
            'current_password' => 'old-password',
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ]);

        $response->assertRedirect(route('account.settings'));
        $response->assertSessionDoesntHaveErrors();

        Auth::logout();

        $this->assertFalse(Auth::attempt(['email' => 'member@example.com', 'password' => 'old-password']));
        $this->assertTrue(Auth::attempt(['email' => 'member@example.com', 'password' => 'new-strong-password']));
    }

    public function test_password_change_is_rejected_with_the_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->post(route('account.settings.password'), [
            'current_password' => 'not-the-right-password',
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ]);

        $response->assertSessionHasErrors('current_password', null, 'updatePassword');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    public function test_password_change_does_not_log_the_user_out_of_the_current_session(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($user)->post(route('account.settings.password'), [
            'current_password' => 'old-password',
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ]);

        $this->assertAuthenticatedAs($user);

        $this->get(route('account.settings'))->assertOk();
    }
}
