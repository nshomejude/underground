<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\MemberProfile;
use App\Models\User;
use App\Services\ConnectionService;
use App\Services\MatchService;
use App\Services\MemberAccess;
use App\Services\NetworkDirectory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class NetworkMemberController extends Controller
{
    public function __construct(
        private readonly NetworkDirectory $directory,
        private readonly MatchService $matches,
        private readonly MemberAccess $access,
        private readonly ConnectionService $connections,
    ) {}

    public function show(Request $request, string $slug): View
    {
        /** @var User $viewer */
        $viewer = $request->user();
        $profile = MemberProfile::query()->where('slug', $slug)->with('user')->first();
        abort_if($profile === null, 404);

        $isSelf = $profile->user_id === $viewer->id;
        $relation = $isSelf ? ['state' => 'self', 'connection' => null] : $this->connections->stateBetween($viewer, $profile->user);

        // Non-listed members are invisible, as are people who blocked the viewer.
        // A member the viewer blocked stays reachable so they can unblock.
        if (! $isSelf) {
            $visible = $this->access->canBeListed($profile->user) && $relation['state'] !== 'blocked_by_them';
            abort_unless($visible, 404);
        }

        $mine = $viewer->profile;
        $match = ! $isSelf && $mine !== null ? $this->matches->score($mine, $profile) : ['score' => null, 'reasons' => []];
        $shared = ! $isSelf && $mine !== null ? $this->matches->sharedSectors($mine, $profile) : [];

        [$connections, $mutualIds] = $this->connectionsOf($profile->user, $viewer, $isSelf);
        $context = $this->directory->tierSlugs([$profile->user]);
        $conversation = $relation['connection']?->isAccepted() ? $relation['connection']->conversation : null;

        return view('network.show', [
            'profile' => $profile,
            'isSelf' => $isSelf,
            'relation' => $relation,
            'conversation' => $conversation,
            'tier' => $context[$profile->user_id] ?? null,
            'tierNames' => $this->directory->tierNames(),
            'companyVerified' => $profile->user->isCompanyVerified(),
            'match' => $match,
            'shared' => $shared,
            'connections' => $connections,
            'connectionCount' => $connections->count(),
            'mutualIds' => $mutualIds,
            'canConnect' => $this->access->canConnect($viewer),
        ]);
    }

    /**
     * Accepted connections of `$member` that are themselves listed, and which of
     * them the viewer also shares.
     *
     * @return array{0: Collection<int, MemberProfile>, 1: list<int>}
     */
    private function connectionsOf(User $member, User $viewer, bool $isSelf): array
    {
        $otherIds = Connection::query()->involving($member->id)->where('status', 'accepted')->get()
            ->map(fn (Connection $c) => $c->requester_id === $member->id ? $c->addressee_id : $c->requester_id)
            ->unique()->values();

        if ($otherIds->isEmpty()) {
            return [collect(), []];
        }

        $profiles = $this->directory->listed()->whereIn('member_profiles.user_id', $otherIds)
            ->with('user')->orderBy('member_profiles.display_name')->get()
            ->reject(fn (MemberProfile $p) => $p->user_id === $viewer->id && ! $isSelf)
            ->values();

        $mutual = [];
        if (! $isSelf) {
            $mine = Connection::query()->involving($viewer->id)->where('status', 'accepted')->get()
                ->map(fn (Connection $c) => $c->requester_id === $viewer->id ? $c->addressee_id : $c->requester_id);
            $mutual = $profiles->pluck('user_id')->intersect($mine)->values()->all();
        }

        return [$profiles, $mutual];
    }
}
