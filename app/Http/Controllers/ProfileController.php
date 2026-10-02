<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\MemberAccess;
use App\Services\ProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** The signed-in member's own profile editor. Always scoped to the current user. */
final class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profiles, private readonly MemberAccess $access) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $profile = $this->profiles->ensureFor($user)->load('user');

        return view('account.profile.edit', [
            'profile' => $profile,
            'checklist' => $this->profiles->checklist($profile),
            'score' => $this->profiles->completeness($profile),
            'canUseNetwork' => $this->access->canUseNetwork($user),
            'isMember' => $this->access->isApprovedMember($user),
            'identityVerified' => $this->access->isIdentityVerified($user),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->profiles->update($request->user(), $request->sectionData());
        $section = (string) $request->input('section');

        return redirect(route('account.profile').'#'.$section)
            ->with('status', 'Saved. Your profile is now '.$this->profiles->ensureFor($request->user())->completeness.'% complete.');
    }
}
