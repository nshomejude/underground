<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\VerificationIdentityDetailsRequest;
use App\Http\Requests\VerificationIdentityFilesRequest;
use App\Models\IdentityVerification;
use App\Services\VerificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Member identity verification: consent, details, upload, review, status. */
final class VerificationIdentityController extends Controller
{
    public function __construct(private readonly VerificationService $service) {}

    public function start(Request $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.index');
        }
        abort_unless($this->service->active($request->user(), 'identity') !== null || $this->service->canStart($request->user(), 'identity'), 403);
        $this->service->startDraft($request->user(), 'identity');

        return redirect()->route('verification.identity.show');
    }

    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.index');
        }

        $row = $this->service->active($user, 'identity');
        if ($row === null) {
            $latest = $this->service->latest($user, 'identity');

            return $latest === null || $latest->status === 'withdrawn'
                ? redirect()->route('verification.index')
                : view('verification.identity.status', $this->statusData($latest));
        }

        if (! $row->isEditable()) {
            return view('verification.identity.status', $this->statusData($row));
        }

        $step = min((int) $request->query('step', $this->reachedStep($row)), $this->reachedStep($row));
        $step = max(1, $step);
        $data = ['row' => $row, 'step' => $step, 'service' => $this->service,
            'files' => $this->service->filesOf($row), 'slots' => $this->service->identitySlots($row->document_type)];
        if ($step === 4) {
            $data['checks'] = $this->service->preChecks($row);
            $data['dob'] = $row->dateOfBirth();
        }
        if ($step === 2) {
            $data['dob'] = $row->dateOfBirth();
        }

        return view('verification.identity.flow', $data);
    }

    public function consent(Request $request): RedirectResponse
    {
        $request->validate(['consent' => ['accepted']], ['consent.accepted' => 'Please confirm you have read and agree to continue.']);
        $row = $this->editable($request);
        $this->service->saveConsent($row, $request->user());

        return redirect()->route('verification.identity.show', ['step' => 2]);
    }

    public function details(VerificationIdentityDetailsRequest $request): RedirectResponse
    {
        $row = $this->editable($request);
        abort_if($row->consent_at === null, 403);
        $this->service->saveIdentityDetails($row, $request->validated());

        return $request->boolean('save_exit')
            ? redirect()->route('verification.index')->with('status', 'Draft saved. You can continue any time.')
            : redirect()->route('verification.identity.show', ['step' => 3]);
    }

    public function files(VerificationIdentityFilesRequest $request): RedirectResponse
    {
        $row = $this->editable($request);
        abort_if($row->document_type === null, 403);

        foreach (['front', 'back', 'selfie'] as $slot) {
            if ($slot === 'back' && ! in_array('back', $this->service->identitySlots($row->document_type), true)) {
                continue;
            }
            if ($request->hasFile($slot)) {
                $this->service->storeFile($row, $slot, $request->file($slot));
            }
        }

        $row->refresh();
        if ($this->service->missingIdentityFiles($row) !== []) {
            return redirect()->route('verification.identity.show', ['step' => 3])
                ->withErrors(['front' => 'Please upload every required side of the document.']);
        }
        $row->forceFill(['last_step' => max((int) $row->last_step, 4)])->save();

        return $request->boolean('save_exit')
            ? redirect()->route('verification.index')->with('status', 'Draft saved. You can continue any time.')
            : redirect()->route('verification.identity.show', ['step' => 4]);
    }

    public function removeFile(Request $request, string $slot): RedirectResponse
    {
        abort_unless(in_array($slot, ['front', 'back', 'selfie'], true), 404);
        $row = $this->editable($request);
        $this->service->removeFile($row, $slot);
        $row->forceFill(['last_step' => min((int) $row->last_step, 3)])->save();

        return redirect()->route('verification.identity.show', ['step' => 3])->with('status', 'File removed.');
    }

    public function submit(Request $request): RedirectResponse
    {
        $request->validate(['checklist' => ['accepted']], ['checklist.accepted' => 'Please tick the confirmation to submit.']);
        $this->service->submit($this->editable($request), $request->user());

        return redirect()->route('verification.identity.show')->with('status', 'Submitted. Our team will review your documents.');
    }

    public function withdraw(Request $request): RedirectResponse
    {
        $row = $this->service->active($request->user(), 'identity');
        abort_if($row === null, 404);
        $this->service->withdraw($row, $request->user());

        return redirect()->route('verification.index')->with('status', 'Your submission was withdrawn and your files were deleted.');
    }

    private function editable(Request $request): IdentityVerification
    {
        abort_unless($request->user()->hasVerifiedEmail(), 403);
        $row = $this->service->active($request->user(), 'identity');
        abort_if($row === null || ! $row->isEditable(), 403, 'There is no draft to edit.');

        return $row;
    }

    private function reachedStep(IdentityVerification $row): int
    {
        if ($row->consent_at === null) {
            return 1;
        }
        if ($row->document_type === null || $row->last_step < 3) {
            return 2;
        }

        return $this->service->missingIdentityFiles($row) === [] && $row->last_step >= 4 ? 4 : 3;
    }

    /** @return array<string, mixed> */
    private function statusData(IdentityVerification $row): array
    {
        return ['row' => $row, 'files' => $this->service->filesOf($row), 'service' => $this->service];
    }
}
