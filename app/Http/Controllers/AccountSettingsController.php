<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAccountSettingsRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Profile and security settings for the authenticated member: changing
 * name/email (email changes are re-confirmed with the current password,
 * since email is the account-recovery-relevant field) and changing the
 * password. Deliberately separate from AccountController, which only ever
 * shows the membership card / application status.
 */
final class AccountSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.settings', ['user' => $request->user()]);
    }

    public function update(UpdateAccountSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $emailChanged = $validated['email'] !== $user->email;

        if ($emailChanged) {
            $currentPassword = $validated['current_password'] ?? '';

            if ($currentPassword === '' || ! Hash::check($currentPassword, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'That password does not match your current password. Re-enter it to confirm this email change.',
                ])->errorBag('updateProfile');
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                report($e);
            }

            return redirect()->route('account.settings')->with('status', 'Your details have been updated. We sent a verification link to '.$user->email.'.');
        }

        return redirect()->route('account.settings')->with('status', 'Your account details have been updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'That password does not match your current password.',
            ])->errorBag('updatePassword');
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        try {
            $user->notify(new PasswordChangedNotification);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('account.security')->with('status', 'Your password has been changed.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['current_password' => ['required', 'string']], [], ['current_password' => 'password']);

        $user = $request->user();

        if (! Hash::check((string) $request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'That password does not match your current password.',
            ])->errorBag('deleteAccount');
        }

        if ($user->is_admin) {
            throw ValidationException::withMessages([
                'current_password' => 'Staff accounts cannot be deleted here. Ask another administrator to remove it.',
            ])->errorBag('deleteAccount');
        }

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Your account has been deleted.');
    }
}
