<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListTeamMembers;
use Illuminate\Contracts\View\View;

/**
 * Leadership bios. The founder gets a real portrait (reused from the
 * landing page); every other principal gets an initials avatar so we
 * never have to fabricate a stock photo of a real-looking person.
 * Content is admin-editable — see Admin\TeamMemberAdminController.
 */
final class TeamController extends Controller
{
    public function __construct(private readonly ListTeamMembers $members) {}

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
}
