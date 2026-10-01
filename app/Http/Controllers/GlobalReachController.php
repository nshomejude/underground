<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ComposeLandingPage;
use Illuminate\Contracts\View\View;

/**
 * Dedicated Global Reach page — the same narrative and engagement models the
 * landing page projects, plus the office network, on a clean URL.
 */
final class GlobalReachController extends Controller
{
    public function __construct(private readonly ComposeLandingPage $composeLandingPage) {}

    public function __invoke(): View
    {
        $landingPage = ($this->composeLandingPage)();

        return view('global-reach.index', [
            'narrative' => $landingPage->narrative,
            'engagementModels' => $landingPage->engagementModels,
            'offices' => OfficeDirectory::all(),
        ]);
    }
}
