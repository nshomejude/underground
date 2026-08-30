<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Staff/member account management: list every registered user and grant
 * or revoke staff admin access. is_admin is deliberately not mass-
 * assignable (see User's #[Fillable] attribute) so a form submission can
 * never grant it by accident — this is the one place it's set directly.
 */
final class UserAdminController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.users.index', [
            'users' => User::query()->orderByDesc('created_at')->get(),
        ]);
    }

    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot change your own admin access.');
        }

        $user->forceFill(['is_admin' => ! $user->is_admin])->save();

        return back()->with('status', sprintf(
            '%s %s admin access.',
            $user->name,
            $user->is_admin ? 'granted' : 'lost',
        ));
    }
}
