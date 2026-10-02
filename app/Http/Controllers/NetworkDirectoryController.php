<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\NetworkDirectoryRequest;
use App\Models\MemberProfile;
use App\Models\User;
use App\Services\MatchService;
use App\Services\MemberAccess;
use App\Services\NetworkDirectory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class NetworkDirectoryController extends Controller
{
    private const PER_PAGE = 12;

    private const SORT_POOL = 300;

    public function __construct(
        private readonly NetworkDirectory $directory,
        private readonly MatchService $matches,
        private readonly MemberAccess $access,
    ) {}

    public function index(NetworkDirectoryRequest $request): View
    {
        /** @var User $viewer */
        $viewer = $request->user();
        $filters = $request->filters();
        $mine = $viewer->profile;

        $query = $this->directory->applyFilters($this->directory->listedFor($viewer)->with('user'), $filters);
        $sort = $filters['sort'];

        if ($sort === 'match' && $mine !== null) {
            $pool = $query->latest('member_profiles.updated_at')->limit(self::SORT_POOL)->get();
            $ranked = $this->matches->rank($viewer, $mine, $pool, 0);
            $page = LengthAwarePaginator::resolveCurrentPage();
            $paginator = new LengthAwarePaginator(
                $ranked->forPage($page, self::PER_PAGE)->values(),
                $ranked->count(),
                self::PER_PAGE,
                $page,
                ['path' => $request->url(), 'query' => $request->query()],
            );
            $cards = $paginator->getCollection();
        } else {
            $query = $sort === 'name'
                ? $query->orderByRaw('LOWER(COALESCE(member_profiles.display_name, \'\'))')
                : $query->latest('member_profiles.id');
            $paginator = $query->paginate(self::PER_PAGE)->withQueryString();
            $cards = $this->decorate($viewer, $mine, $paginator->getCollection());
            $paginator->setCollection($cards);
        }

        $context = $this->context($cards->map(fn (array $c) => $c['profile']));
        $top = ! $request->hasActiveFilters() && $paginator->currentPage() === 1 && $mine !== null
            ? $this->matches->suggestions($viewer, 3)
            : collect();

        return view('network.index', [
            'cards' => $cards,
            'paginator' => $paginator,
            'filters' => $filters,
            'active' => $request->hasActiveFilters(),
            'top' => $top,
            'tierNames' => $context['tierNames'],
            'company' => $context['company'],
            'countries' => $this->directory->countries(),
            'viewer' => $this->viewerStatus($viewer),
        ]);
    }

    public function matches(NetworkDirectoryRequest $request): View
    {
        /** @var User $viewer */
        $viewer = $request->user();
        $suggestions = $this->matches->suggestions($viewer, 24);
        $context = $this->context($suggestions->map(fn (array $c) => $c['profile']));

        return view('network.matches', [
            'cards' => $suggestions,
            'tierNames' => $context['tierNames'],
            'company' => $context['company'],
            'viewer' => $this->viewerStatus($viewer),
        ]);
    }

    /**
     * Attach score/reasons/tier to a page of profiles for non-match sorts.
     *
     * @param  Collection<int, MemberProfile>  $profiles
     * @return Collection<int, array{profile: MemberProfile, score: ?int, reasons: list<string>, tier: ?string}>
     */
    private function decorate(User $viewer, ?MemberProfile $mine, Collection $profiles): Collection
    {
        $tiers = $this->directory->tierSlugs($profiles->map(fn (MemberProfile $p) => $p->user));
        $myTier = $mine ? ($this->directory->tierSlugs([$viewer])[$viewer->id] ?? null) : null;
        $rank = fn (?string $s) => $s === null ? 0 : (int) config('network.tier_ranks.'.$s, 1);

        return $profiles->map(function (MemberProfile $p) use ($mine, $tiers, $myTier, $rank): array {
            $slug = $tiers[$p->user_id] ?? null;
            $m = $mine ? $this->matches->score($mine, $p, $rank($myTier), $rank($slug)) : ['score' => null, 'reasons' => []];

            return ['profile' => $p, 'score' => $m['score'], 'reasons' => $m['reasons'], 'tier' => $slug];
        })->values();
    }

    /**
     * @param  Collection<int, MemberProfile>  $profiles
     * @return array{tierNames: array<string, string>, company: array<int, true>}
     */
    private function context(Collection $profiles): array
    {
        return [
            'tierNames' => $this->directory->tierNames(),
            'company' => $this->directory->companyVerified($profiles->pluck('user_id')->map(fn ($i) => (int) $i)->all()),
        ];
    }

    /** @return array{profile: ?MemberProfile, verified: bool, listed: bool, hidden: bool} */
    private function viewerStatus(User $viewer): array
    {
        $profile = $viewer->profile;

        return [
            'profile' => $profile,
            'verified' => $this->access->isIdentityVerified($viewer),
            'listed' => $this->access->canBeListed($viewer),
            'hidden' => ($profile?->visibility ?? 'members') === 'hidden',
        ];
    }
}
