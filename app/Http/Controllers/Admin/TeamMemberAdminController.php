<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeamMemberRequest;
use Domain\Content\Entities\TeamMember;
use Domain\Content\Repositories\TeamMemberRepository;
use Domain\Content\ValueObjects\EmploymentStatus;
use Domain\Content\ValueObjects\TeamRoleCategory;
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
            roleCategory: TeamRoleCategory::from($data['role_category']),
            employmentStatus: EmploymentStatus::from($data['employment_status']),
            languages: self::parseLanguages($data['languages'] ?? null),
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
                roleCategory: TeamRoleCategory::from($data['role_category']),
                employmentStatus: EmploymentStatus::from($data['employment_status']),
                languages: self::parseLanguages($data['languages'] ?? null),
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

    /**
     * The admin form carries languages as one comma-separated line (e.g.
     * "English, French, Arabic") rather than a repeatable field set — see
     * NarrativeAdminController::update() for the equivalent newline-split
     * convention this mirrors for a single-line list.
     *
     * @return list<string>
     */
    private static function parseLanguages(?string $raw): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', (string) $raw)),
            static fn (string $language): bool => $language !== '',
        ));
    }
}
