<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * General contact information. The real intake mechanism is the
 * Confidential Inquiry form (Engagement context) — this page only
 * orients a visitor toward it, plus the office network and departmental
 * mailboxes for correspondence that isn't a new mandate.
 */
final class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact.index', [
            'generalEmail' => 'info@un-der.com',
            'generalPhone' => '+1-571-508-9170',
            'offices' => OfficeDirectory::all(),
            'departments' => OfficeDirectory::departments(),
        ]);
    }
}
