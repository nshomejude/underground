<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Domain\Content\Repositories\SiteSettingRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Member account registration: standard Laravel session auth built on core
 * framework primitives only (Auth/Hash facades, session) — no
 * Breeze/Fortify/Jetstream. Registering does not itself grant membership;
 * see MembershipController for the vetted application process. An account
 * is simply the credential a member logs into /account with once their
 * application clears review.
 *
 * Gated by the "Public Registration" toggle in Site Configuration — while
 * disabled, both routes bounce to /login with an explanatory flash message
 * rather than exposing the form.
 */
final class RegisterController extends Controller
{
    public function __construct(private readonly SiteSettingRepository $settings) {}

    public function create(): View|RedirectResponse
    {
        if (! $this->settings->current()->publicRegistrationEnabled) {
            return $this->registrationClosed();
        }

        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        if (! $this->settings->current()->publicRegistrationEnabled) {
            return $this->registrationClosed();
        }

        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->sendEmailVerificationNotification();

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('account.show');
    }

    private function registrationClosed(): RedirectResponse
    {
        return redirect()->route('login')->with('error', 'New registrations are temporarily closed. Please check back later.');
    }
}
