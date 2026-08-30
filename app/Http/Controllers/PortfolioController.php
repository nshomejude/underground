<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListPortfolioEngagements;
use Illuminate\Contracts\View\View;

/**
 * Selected past engagements, anonymised. Every entry is tagged with one
 * of the six sectors the firm operates in — no client, government, or
 * company is ever named, consistent with the confidentiality every
 * mandate is run under. Content is admin-editable — see
 * Admin\PortfolioEngagementAdminController.
 */
final class PortfolioController extends Controller
{
    public function __construct(private readonly ListPortfolioEngagements $engagements) {}

    public function index(): View
    {
        return view('portfolio.index', [
            'engagements' => array_map(
                static fn ($engagement) => $engagement->toArray(),
                ($this->engagements)(),
            ),
        ]);
    }
}
