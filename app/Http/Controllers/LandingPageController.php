<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ComposeLandingPage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

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
            'founderPortraitSrc' => asset('images/founder-portrait.jpg'),
        ]);
    }
}
