<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAvatarRequest;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ProfileAvatarController extends Controller
{
    public function __construct(private readonly ProfileService $profiles) {}

    public function store(ProfileAvatarRequest $request): RedirectResponse
    {
        $this->profiles->storeAvatar($request->user(), $request->file('avatar'));

        return redirect(route('account.profile').'#identity')->with('status', 'Your photo has been updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->profiles->removeAvatar($request->user());

        return redirect(route('account.profile').'#identity')->with('status', 'Your photo has been removed.');
    }
}
