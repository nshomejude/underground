<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\NetworkConnectRequest;
use App\Models\Connection;
use App\Models\MemberProfile;
use App\Models\User;
use App\Services\ConnectionService;
use App\Services\MemberAccess;
use App\Services\NetworkDirectory;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class NetworkConnectionController extends Controller
{
    public function __construct(
        private readonly ConnectionService $connections,
        private readonly NetworkDirectory $directory,
        private readonly MemberAccess $access,
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $tab = in_array($request->query('tab'), ['connections', 'received', 'sent', 'blocked'], true) ? $request->query('tab') : 'connections';

        $base = fn () => Connection::query()->involving($user->id);
        $load = ['requester.profile', 'addressee.profile', 'conversation'];

        $accepted = $base()->where('status', 'accepted')->with($load)->latest('responded_at')->get();
        $received = Connection::query()->where('addressee_id', $user->id)->where('status', 'pending')->with($load)->latest()->get();
        $sent = Connection::query()->where('requester_id', $user->id)->whereIn('status', ['pending', 'declined'])->with($load)->latest()->get();
        $blocked = Connection::query()->where('blocked_by', $user->id)->where('status', 'blocked')->with($load)->latest()->get();

        $others = $accepted->merge($received)->merge($sent)->merge($blocked)
            ->map(fn (Connection $c) => $c->otherParty($user))->unique('id');

        return view('network.connections', [
            'tab' => $tab,
            'accepted' => $accepted,
            'received' => $received,
            'sent' => $sent,
            'blocked' => $blocked,
            'viewer' => $user,
            'tiers' => $this->directory->tierSlugs($others),
            'tierNames' => $this->directory->tierNames(),
        ]);
    }

    public function connect(NetworkConnectRequest $request, string $slug): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $target = $this->target($slug);
        $data = $request->validated();

        try {
            $this->connections->request(
                $user,
                $target,
                $data['kind'],
                trim($data['message']),
                isset($data['topic']) ? trim($data['topic']) : null,
                $data['sectors'] ?? [],
            );
        } catch (DomainException $e) {
            return back()->withInput()->with('network_error', $e->getMessage());
        }

        return redirect()->route('network.show', $slug)->with('status', $data['kind'] === 'collaborate'
            ? 'Collaboration request sent.'
            : 'Connection request sent.');
    }

    public function respond(Request $request, Connection $connection): RedirectResponse
    {
        $data = $request->validate(['action' => ['required', 'in:accept,decline']]);

        try {
            $this->connections->respond($connection, $request->user(), $data['action'] === 'accept');
        } catch (DomainException $e) {
            return back()->with('network_error', $e->getMessage());
        }

        return redirect()->route('network.connections', ['tab' => $data['action'] === 'accept' ? 'connections' : 'received'])
            ->with('status', $data['action'] === 'accept' ? 'Connected. You can now message each other.' : 'Request declined.');
    }

    public function withdraw(Request $request, Connection $connection): RedirectResponse
    {
        try {
            $this->connections->withdraw($connection, $request->user());
        } catch (DomainException $e) {
            return back()->with('network_error', $e->getMessage());
        }

        return redirect()->route('network.connections', ['tab' => 'sent'])->with('status', 'Request withdrawn.');
    }

    public function remove(Request $request, Connection $connection): RedirectResponse
    {
        try {
            $this->connections->remove($connection, $request->user());
        } catch (DomainException $e) {
            return back()->with('network_error', $e->getMessage());
        }

        return redirect()->route('network.connections')->with('status', 'Connection removed.');
    }

    public function block(Request $request, string $slug): RedirectResponse
    {
        $this->connections->block($request->user(), $this->target($slug));

        return redirect()->route('network.connections', ['tab' => 'blocked'])->with('status', 'Member blocked. They can no longer see or contact you.');
    }

    public function unblock(Request $request, string $slug): RedirectResponse
    {
        $this->connections->unblock($request->user(), $this->target($slug));

        return redirect()->route('network.show', $slug)->with('status', 'Member unblocked.');
    }

    /** The member behind a slug; unlisted members only resolve when a connection row already exists. */
    private function target(string $slug): User
    {
        $profile = MemberProfile::query()->where('slug', $slug)->with('user')->first();
        abort_if($profile === null, 404);

        $viewer = request()->user();
        $visible = $this->access->canBeListed($profile->user)
            || Connection::query()->between($viewer->id, $profile->user_id)->exists();
        abort_unless($visible, 404);

        return $profile->user;
    }
}
