<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ComposeLandingPage;
use Application\Content\Queries\ListOffices;
use Illuminate\Contracts\View\View;

/**
 * Dedicated Global Reach page — the same narrative and engagement models the
 * landing page projects, plus the alliance network overview, on a clean
 * URL. See /global-alliance for the alliance model itself and /locations
 * for the full, status-labelled directory.
 */
final class GlobalReachController extends Controller
{
    public function __construct(
        private readonly ComposeLandingPage $composeLandingPage,
        private readonly ListOffices $offices,
    ) {}

    public function __invoke(): View
    {
        $landingPage = ($this->composeLandingPage)();

        return view('global-reach.index', [
            'narrative' => $landingPage->narrative,
            'engagementModels' => $landingPage->engagementModels,
            'offices' => ($this->offices)(),
        ]);
    }
}
