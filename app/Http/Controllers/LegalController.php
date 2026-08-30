<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Terms of Service and Privacy Policy. Genuinely reasonable B2B-advisory
 * copy — not exhaustive boilerplate, not lorem ipsum.
 */
final class LegalController extends Controller
{
    public function terms(): View
    {
        return view('legal.terms', [
            'effectiveDate' => '1 January 2026',
        ]);
    }

    public function privacy(): View
    {
        return view('legal.privacy', [
            'effectiveDate' => '1 January 2026',
        ]);
    }
}
