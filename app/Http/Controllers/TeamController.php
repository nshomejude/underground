<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListGalleryPhotos;
use Application\Content\Queries\ListPortfolioEngagements;
use Application\Content\Queries\ListTeamMembers;
use Domain\Content\Entities\TeamMember;
use Domain\Content\ValueObjects\TeamRoleCategory;
use Illuminate\Contracts\View\View;

/**
 * Leadership bios. The founder gets a real portrait (reused from the
 * landing page); every other principal gets an initials avatar so we
 * never have to fabricate a stock photo of a real-looking person.
 * Content is admin-editable — see Admin\TeamMemberAdminController.
 *
 * seniorLeadership(), gallery() and caseStudies() are the three
 * additional Team pages from the brief's restructure: a 3-tab Governance/
 * Executive split, a photo gallery, and the relocated former /portfolio
 * case studies (see Admin\PortfolioEngagementAdminController, which this
 * reuses unchanged — case studies are still PortfolioEngagement rows).
 */
final class TeamController extends Controller
{
    public function __construct(
        private readonly ListTeamMembers $members,
        private readonly ListGalleryPhotos $photos,
        private readonly ListPortfolioEngagements $engagements,
    ) {}

    public function index(): View
    {
        return view('team.index', [
            'founderPortraitSrc' => asset('images/founder-portrait.jpg'),
            'leaders' => array_map(
                static fn ($member) => $member->toArray(),
                ($this->members)(),
            ),
        ]);
    }

    /**
     * The 3-tab Senior Leadership page: Executive Officers, Board Members,
     * Advisory Board. The latter two are the Governance half of
     * roleCategory, split by title text (see TeamMember::isBoardMember()/
     * isAdvisoryBoard()) since the roster only tracks one Governance
     * category, not two. Regional and Expertise principals are not
     * "senior leadership" in the brief's governance/executive sense and
     * are left off this page — they remain on the main /team roster.
     */
    public function seniorLeadership(): View
    {
        $all = ($this->members)();

        return view('team.senior-leadership', [
            'founderPortraitSrc' => asset('images/founder-portrait.jpg'),
            'executives' => $this->toArrays(array_filter(
                $all,
                static fn (TeamMember $member): bool => $member->roleCategory === TeamRoleCategory::Executive,
            )),
            'boardMembers' => $this->toArrays(array_filter(
                $all,
                static fn (TeamMember $member): bool => $member->isBoardMember(),
            )),
            'advisoryBoard' => $this->toArrays(array_filter(
                $all,
                static fn (TeamMember $member): bool => $member->isAdvisoryBoard(),
            )),
        ]);
    }

    /**
     * Photo grid. There are no real, rights-cleared photos seeded yet —
     * see GalleryPhotoSeeder — so this renders a graceful empty state
     * rather than any placeholder imagery when $photos is empty.
     */
    public function gallery(): View
    {
        return view('team.gallery', [
            'photos' => array_map(
                static fn ($photo) => $photo->toArray(),
                ($this->photos)(),
            ),
        ]);
    }

    /**
     * Case studies, relocated from the former /portfolio page into
     * Team's structure. This is the redirect target for /portfolio (see
     * routes/lot1-team.php's doc block) and reuses PortfolioEngagement
     * unchanged, now with its case-study field set (realRole,
     * deliverables, period, publicationAgreementNote).
     */
    public function caseStudies(): View
    {
        return view('team.case-studies', [
            'engagements' => array_map(
                static fn ($engagement) => $engagement->toArray(),
                ($this->engagements)(),
            ),
        ]);
    }

    /**
     * @param  iterable<TeamMember>  $members
     * @return list<array>
     */
    private function toArrays(iterable $members): array
    {
        return array_values(array_map(
            static fn (TeamMember $member): array => $member->toArray(),
            is_array($members) ? $members : iterator_to_array($members),
        ));
    }
}
