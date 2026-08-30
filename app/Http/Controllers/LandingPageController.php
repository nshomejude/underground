<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ComposeLandingPage;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Route;
use Throwable;

/**
 * The flagship page: everything on it is assembled once by
 * ComposeLandingPage and handed to the view, so this page and the
 * `GET /api/v1/landing-page` endpoint can never drift apart.
 */
final class LandingPageController extends Controller
{
    public function __construct(private readonly ComposeLandingPage $composeLandingPage) {}

    public function index(): View
    {
        return view('welcome', [
            'landingPage' => ($this->composeLandingPage)(),
            'aboutHref' => Route::has('about') ? route('about') : '#',
            'founderPortraitSrc' => $this->founderPortraitSrc(),
        ]);
    }

    /**
     * Resolved through Vite so the asset is fingerprinted in production;
     * falls back to a plain public path when no build manifest exists yet
     * (e.g. the test environment, or before `npm run build` has run).
     */
    private function founderPortraitSrc(): string
    {
        try {
            return app(Vite::class)->asset('resources/images/founder-portrait.jpg');
        } catch (Throwable) {
            return asset('images/founder-portrait.jpg');
        }
    }
}
