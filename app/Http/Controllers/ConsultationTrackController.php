<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Application\Consultation\Queries\TrackConsultationRequest;
use Domain\Shared\Exceptions\DomainException;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public, no-login status lookup for a consultation request: the only
 * credential a requester holds is the reference they were given on
 * submission, so this page trades that reference for status only — never
 * the project/need descriptions or contact details that only the
 * submitter (or staff) should see. Mirrors App\Http\Controllers\
 * InquiryTrackController; see resources/views/consultations/track.blade.php.
 */
final class ConsultationTrackController extends Controller
{
    public function __construct(private readonly TrackConsultationRequest $track) {}

    public function show(Request $request): View
    {
        $reference = trim((string) $request->query('reference', ''));
        $consultation = null;
        $searched = $reference !== '';

        if ($searched) {
            try {
                $consultation = ($this->track)($reference);
            } catch (DomainException) {
                $consultation = null;
            }
        }

        return view('consultations.track', [
            'reference' => $reference,
            'consultation' => $consultation,
            'searched' => $searched,
        ]);
    }
}
