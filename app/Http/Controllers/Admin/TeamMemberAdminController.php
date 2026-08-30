<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeamMemberRequest;
use Domain\Content\Entities\TeamMember;
use Domain\Content\Repositories\TeamMemberRepository;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin CRUD for TeamMember — the /team leadership bios. See
 * Domain\Content\Entities\TeamMember.
 */
final class TeamMemberAdminController extends Controller
{
    public function __construct(private readonly TeamMemberRepository $members) {}

    public function index(): View
    {
        return view('admin.team.index', ['members' => $this->members->all()]);
    }

    public function create(): View
    {
        return view('admin.team.create');
    }

    public function store(TeamMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->members->save(new TeamMember(
            slug: Slug::fromString($data['slug']),
            name: $data['name'],
            title: $data['title'],
            background: $data['background'],
            icon: $data['icon'] ?? null,
            hasPortrait: (bool) ($data['has_portrait'] ?? false),
            position: (int) $data['position'],
        ));

        return redirect()->route('admin.team.index')->with('status', 'Team member created.');
    }

    public function edit(string $team_member): View
    {
        $found = $this->members->findBySlug(Slug::fromString($team_member));

        if ($found === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known team member.', $team_member));
        }

        return view('admin.team.edit', ['member' => $found]);
    }

    public function update(string $team_member, TeamMemberRequest $request): RedirectResponse
    {
        $original = $this->members->findBySlug(Slug::fromString($team_member));

        if ($original === null) {
            throw new NotFoundHttpException(sprintf('"%s" is not a known team member.', $team_member));
        }

        $data = $request->validated();

        $this->members->save(
            new TeamMember(
                slug: Slug::fromString($data['slug']),
                name: $data['name'],
                title: $data['title'],
                background: $data['background'],
                icon: $data['icon'] ?? null,
                hasPortrait: (bool) ($data['has_portrait'] ?? false),
                position: (int) $data['position'],
            ),
            originalSlug: $original->slug,
        );

        return redirect()->route('admin.team.index')->with('status', 'Team member updated.');
    }

    public function destroy(string $team_member): RedirectResponse
    {
        $this->members->delete(Slug::fromString($team_member));

        return redirect()->route('admin.team.index')->with('status', 'Team member removed.');
    }
}
