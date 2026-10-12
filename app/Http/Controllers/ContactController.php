<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Content\Queries\ListOffices;
use Illuminate\Contracts\View\View;

/**
 * General contact information. The real intake mechanism is the
 * Confidential Inquiry form (Engagement context) — this page only
 * orients a visitor toward it, plus the office network and departmental
 * mailboxes for correspondence that isn't a new mandate.
 */
final class ContactController extends Controller
{
    public function __construct(private readonly ListOffices $offices) {}

    public function index(): View
    {
        return view('contact.index', [
            'generalEmail' => 'info@un-der.com',
            'generalPhone' => '+1-571-508-9170',
            // Only show locations with a real point on the map here — the
            // broader regional/continental coverage entries and anything
            // still Planned are not a place to write to, so they stay on
            // /locations rather than this correspondence page.
            'offices' => array_values(array_filter(
                ($this->offices)(),
                static fn ($office): bool => $office->hasCoordinates(),
            )),
            'departments' => OfficeDirectory::departments(),
        ]);
    }
}
