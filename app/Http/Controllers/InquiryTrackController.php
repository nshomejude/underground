<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Engagement\Queries\TrackInquiry;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public, no-login status lookup for a confidential inquiry: the only
 * credential a prospective client holds is the reference they were given on
 * submission, so this page trades that reference for status only — never the
 * brief, organisation, or contact details that only the submitter (or staff)
 * should see. See resources/views/inquiries/track.blade.php.
 */
final class InquiryTrackController extends Controller
{
    public function __construct(private readonly TrackInquiry $track) {}

    public function show(Request $request): View
    {
        $reference = trim((string) $request->query('reference', ''));
        $inquiry = null;
        $searched = $reference !== '';

        if ($searched) {
            try {
                $inquiry = ($this->track)($reference);
            } catch (DomainException) {
                $inquiry = null;
            }
        }

        return view('inquiries.track', [
            'reference' => $reference,
            'inquiry' => $inquiry,
            'searched' => $searched,
        ]);
    }
}
