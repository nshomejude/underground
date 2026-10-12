<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListOffices;
use Illuminate\Contracts\View\View;

/**
 * The full alliance directory: every office and regional coverage entry
 * with its status and relationship type labelled plainly — the
 * "show your work" transparency page behind /global-reach and
 * /global-alliance.
 */
final class LocationsController extends Controller
{
    public function __construct(private readonly ListOffices $offices) {}

    public function __invoke(): View
    {
        return view('locations.index', [
            'offices' => ($this->offices)(),
        ]);
    }
}
