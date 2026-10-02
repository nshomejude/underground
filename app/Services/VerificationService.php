<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CompanyVerification;
use App\Models\IdentityVerification;
use App\Models\User;
use App\Models\VerificationEvent;
use App\Notifications\VerificationDecisionNotification;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Identity and company verification: secure upload plus manual review.
 * Nothing here matches faces or reads documents; the "pre-checks" are cheap,
 * real local checks (readable file, size, mime, dimensions).
 */
final class VerificationService
{
    public const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
    ];

    /** Document types that have a back side. */
    private const TWO_SIDED = ['national_id', 'drivers_licence', 'residence_permit'];

    public function disk(): Filesystem
    {
        return Storage::disk((string) config('network.verification_disk', 'local'));
    }

    /**
     * Public status summary for badges, the hub and other workstreams.
     *
     * @return array{identity: string, company: string}  none|draft|submitted|in_review|approved|rejected|expired
     */
    public function statusFor(User $user): array
    {
        return [
            'identity' => $this->summarise($user->identityVerifications()->latest('id')->get()),
            'company' => $this->summarise($user->companyVerifications()->latest('id')->get()),
        ];
    }

    /** @param  \Illuminate\Support\Collection<int, IdentityVerification|CompanyVerification>  $rows */
    private function summarise($rows): string
    {
        if ($rows->contains(fn ($r) => $r->isApproved())) {
            return 'approved';
        }

        $latest = $rows->first();

        if ($latest === null || $latest->status === 'withdrawn') {
            return 'none';
        }

        if ($latest->status === 'approved') {
            return 'expired';
        }

        return (string) $latest->status;
    }

    public function model(string $kind): string
    {
        return $kind === 'company' ? CompanyVerification::class : IdentityVerification::class;
    }

    public function latest(User $user, string $kind): IdentityVerification|CompanyVerification|null
    {
        $relation = $kind === 'company' ? $user->companyVerifications() : $user->identityVerifications();

        return $relation->latest('id')->first();
    }

    public function active(User $user, string $kind): IdentityVerification|CompanyVerification|null
    {
        $relation = $kind === 'company' ? $user->companyVerifications() : $user->identityVerifications();

        return $relation->whereIn('status', ['draft', 'submitted', 'in_review'])->latest('id')->first();
    }

    /** May the member begin a new submission (none open; not currently approved for long)? */
    public function canStart(User $user, string $kind): bool
    {
        if ($this->active($user, $kind) !== null) {
            return false;
        }

        $approved = ($kind === 'company' ? $user->companyVerifications() : $user->identityVerifications())
            ->where('status', 'approved')->where('expires_at', '>', now())->latest('expires_at')->first();

        return $approved === null || $approved->expires_at->lte(now()->addDays(60));
    }

    /** Existing open submission, or a fresh draft. */
    public function startDraft(User $user, string $kind): IdentityVerification|CompanyVerification
    {
        $active = $this->active($user, $kind);
        if ($active !== null) {
            return $active;
        }

        $attributes = ['user_id' => $user->id, 'status' => 'draft', 'last_step' => 1];
        if ($kind === 'company') {
            $attributes['company_name'] = '';
        }

        $row = $this->model($kind)::create($attributes);
        $this->record($row, 'draft_started', $user);

        return $row;
    }

    public function record(IdentityVerification|CompanyVerification $row, string $event, ?User $actor = null, ?string $note = null, array $meta = []): VerificationEvent
    {
        return VerificationEvent::create([
            'kind' => $row::KIND,
            'verification_id' => $row->id,
            'actor_id' => $actor?->id,
            'event' => $event,
            'note' => $note,
            'meta' => $meta ?: null,
        ]);
    }

    public function saveConsent(IdentityVerification|CompanyVerification $row, User $actor): void
    {
        $row->forceFill([
            'consent_at' => now(),
            'consent_version' => (string) config('verification.consent_version'),
            'last_step' => max((int) $row->last_step, 2),
        ])->save();
        $this->record($row, 'consent_given', $actor, null, ['version' => $row->consent_version]);
    }

    public function saveIdentityDetails(IdentityVerification $row, array $data): void
    {
        $row->forceFill([
            'document_type' => $data['document_type'],
            'document_country' => $data['document_country'],
            'full_name_on_document' => $data['full_name_on_document'],
            'date_of_birth_encrypted' => encrypt($data['date_of_birth']),
            'document_expiry' => $data['document_expiry'],
            'document_number_last4' => strtoupper(substr(preg_replace('/\s+/', '', $data['document_number_last4']), -4)),
            'last_step' => max((int) $row->last_step, 3),
        ])->save();

        // A passport has no back side; drop a stale one if the type changed.
        if (! in_array($row->document_type, self::TWO_SIDED, true) && $this->filesOf($row)['back'] ?? null) {
            $this->removeFile($row, 'back');
        }
    }

    public function saveCompanyDetails(CompanyVerification $row, array $data): void
    {
        $row->forceFill([
            'company_name' => $data['company_name'],
            'trading_name' => $data['trading_name'] ?? null,
            'registration_number' => $data['registration_number'],
            'country' => $data['country'],
            'incorporation_date' => $data['incorporation_date'],
            'website' => $data['website'] ?? null,
            'registered_address' => $data['registered_address'],
            'applicant_role' => $data['applicant_role'],
            'sectors' => array_values($data['sectors'] ?? []),
            'last_step' => max((int) $row->last_step, 3),
        ])->save();
    }

    /** @return array<string, array{path: string, name: string, mime: string, size: int, width: ?int, height: ?int}> */
    public function filesOf(IdentityVerification|CompanyVerification $row): array
    {
        return (array) ($row instanceof CompanyVerification ? $row->documents : $row->files);
    }

    private function setFiles(IdentityVerification|CompanyVerification $row, array $files): void
    {
        $row->forceFill([$row instanceof CompanyVerification ? 'documents' : 'files' => $files ?: null])->save();
    }

    public function storeFile(IdentityVerification|CompanyVerification $row, string $slot, UploadedFile $file): void
    {
        $mime = (string) $file->getMimeType();
        $ext = self::MIME_EXTENSIONS[$mime] ?? null;
        if ($ext === null) {
            throw ValidationException::withMessages([$slot => 'This file type is not accepted.']);
        }

        $size = null;
        if (str_starts_with($mime, 'image/') && ($info = @getimagesize($file->getRealPath())) !== false) {
            $size = [$info[0], $info[1]];
        }

        $dir = trim((string) config('network.verification_path', 'verification'), '/').'/'.$row->user_id;
        $path = $dir.'/'.Str::uuid().'.'.$ext;
        $this->disk()->put($path, (string) file_get_contents($file->getRealPath()));

        $files = $this->filesOf($row);
        if (isset($files[$slot]['path'])) {
            $this->disk()->delete($files[$slot]['path']);
        }

        $files[$slot] = [
            'path' => $path,
            'name' => Str::limit(preg_replace('/[^\w .()-]+/u', '', $file->getClientOriginalName()) ?: 'upload.'.$ext, 80, ''),
            'mime' => $mime,
            'size' => (int) $file->getSize(),
            'width' => $size[0] ?? null,
            'height' => $size[1] ?? null,
        ];
        $this->setFiles($row, $files);
    }

    public function removeFile(IdentityVerification|CompanyVerification $row, string $slot): void
    {
        $files = $this->filesOf($row);
        if (isset($files[$slot]['path'])) {
            $this->disk()->delete($files[$slot]['path']);
        }
        unset($files[$slot]);
        $this->setFiles($row, $files);
    }

    /** Delete every stored file of a submission. */
    public function deleteFiles(IdentityVerification|CompanyVerification $row): int
    {
        $count = 0;
        foreach ($this->filesOf($row) as $file) {
            if (isset($file['path']) && $this->disk()->delete($file['path'])) {
                $count++;
            }
        }
        $this->setFiles($row, []);
        $row->forceFill(['files_purged_at' => now()])->save();

        return $count;
    }

    /** @return list<string> */
    public function identitySlots(?string $documentType): array
    {
        return in_array($documentType, self::TWO_SIDED, true) ? ['front', 'back'] : ['front'];
    }

    /** @return list<string> */
    public function missingIdentityFiles(IdentityVerification $row): array
    {
        $files = $this->filesOf($row);

        return array_values(array_filter($this->identitySlots($row->document_type), fn ($s) => empty($files[$s]['path'])));
    }

    /** @return list<string> */
    public function missingCompanyDocuments(CompanyVerification $row): array
    {
        $files = $this->filesOf($row);

        return array_values(array_filter(['registration_certificate', 'proof_of_address'], fn ($s) => empty($files[$s]['path'])));
    }

    /**
     * Honest, cheap local checks on what was actually uploaded.
     *
     * @return list<array{label: string, ok: bool, detail: string}>
     */
    public function preChecks(IdentityVerification|CompanyVerification $row): array
    {
        $checks = [];
        $min = (int) config('verification.min_image_dimension', 600);
        $max = ($row instanceof CompanyVerification ? (int) config('verification.company_max_kb') : (int) config('verification.identity_max_kb')) * 1024;

        foreach ($this->filesOf($row) as $slot => $file) {
            $label = $this->slotLabel($slot);
            $exists = $this->disk()->exists($file['path'] ?? '');
            $checks[] = ['label' => $label.': file readable', 'ok' => $exists, 'detail' => $exists ? $file['name'] : 'Upload again'];
            if (! $exists) {
                continue;
            }
            $checks[] = ['label' => $label.': size and type', 'ok' => ($file['size'] ?? 0) <= $max && isset(self::MIME_EXTENSIONS[$file['mime'] ?? '']),
                'detail' => number_format(($file['size'] ?? 0) / 1048576, 1).' MB, '.strtoupper(self::MIME_EXTENSIONS[$file['mime'] ?? ''] ?? '?')];
            if (str_starts_with((string) ($file['mime'] ?? ''), 'image/')) {
                $long = max((int) ($file['width'] ?? 0), (int) ($file['height'] ?? 0));
                $checks[] = ['label' => $label.': resolution', 'ok' => $long >= $min, 'detail' => ($file['width'] ?? '?').' x '.($file['height'] ?? '?').' px'];
            }
        }

        return $checks;
    }

    public function slotLabel(string $slot): string
    {
        return config('network.company_document_types.'.$slot) ?? match ($slot) {
            'front' => 'Document front',
            'back' => 'Document back',
            'selfie' => 'Selfie with document',
            default => ucfirst(str_replace('_', ' ', $slot)),
        };
    }

    /** Move a complete draft into the review queue. */
    public function submit(IdentityVerification|CompanyVerification $row, User $actor): void
    {
        if (! $row->isEditable()) {
            throw ValidationException::withMessages(['submission' => 'This submission has already been sent.']);
        }

        $missing = $row instanceof CompanyVerification ? $this->missingCompanyDocuments($row) : $this->missingIdentityFiles($row);
        $incomplete = $row->consent_at === null
            || ($row instanceof CompanyVerification ? $row->company_name === '' || $row->registration_number === null : $row->full_name_on_document === null || $row->document_expiry === null);
        if ($missing !== [] || $incomplete) {
            throw ValidationException::withMessages(['submission' => 'Please finish every step and upload the required documents before submitting.']);
        }
        if ($row instanceof IdentityVerification && $row->document_expiry->isPast()) {
            throw ValidationException::withMessages(['submission' => 'This document has expired. Please use a valid one.']);
        }
        foreach ($this->preChecks($row) as $check) {
            if (! $check['ok']) {
                throw ValidationException::withMessages(['submission' => $check['label'].' did not pass. Please replace the file.']);
            }
        }

        $row->forceFill(['status' => 'submitted', 'submitted_at' => now(), 'last_step' => 5, 'info_request' => null])->save();
        $this->record($row, 'submitted', $actor);
        $actor->notify(new VerificationDecisionNotification($row::KIND, 'submitted'));
    }

    /** Right to withdraw: removes all files and personal data; drafts disappear entirely. */
    public function withdraw(IdentityVerification|CompanyVerification $row, User $actor): void
    {
        $this->deleteFiles($row);

        if ($row->status === 'draft') {
            VerificationEvent::where('kind', $row::KIND)->where('verification_id', $row->id)->delete();
            $row->delete();

            return;
        }

        $attrs = ['status' => 'withdrawn'];
        if ($row instanceof IdentityVerification) {
            $attrs['date_of_birth_encrypted'] = null;
        }
        $row->forceFill($attrs)->save();
        $this->record($row, 'withdrawn', $actor);
    }

    public function markInReview(IdentityVerification|CompanyVerification $row, User $reviewer): void
    {
        if ($row->status !== 'submitted') {
            return;
        }
        $row->forceFill(['status' => 'in_review', 'in_review_at' => now()])->save();
        $this->record($row, 'in_review', $reviewer);
    }

    public function approve(IdentityVerification|CompanyVerification $row, User $reviewer, ?string $notes = null): void
    {
        $this->assertReviewable($row, $reviewer);
        $row->forceFill([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'expires_at' => now()->addMonths((int) config('network.verification_valid_months', 24)),
            'rejection_reason' => null,
            'info_request' => null,
            'reviewer_notes' => $notes ?? $row->reviewer_notes,
        ])->save();
        $this->record($row, 'approved', $reviewer, $notes);
        $row->user->notify(new VerificationDecisionNotification($row::KIND, 'approved', ['expires' => $row->expires_at->format('j F Y')]));
    }

    public function reject(IdentityVerification|CompanyVerification $row, User $reviewer, string $reasonKey, ?string $message = null, ?string $notes = null): void
    {
        $this->assertReviewable($row, $reviewer);
        $reason = (string) config('verification.rejection_reasons.'.$reasonKey, $reasonKey);
        $full = trim($reason.($message ? ': '.$message : ''));
        $row->forceFill([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $full,
            'info_request' => null,
            'reviewer_notes' => $notes ?? $row->reviewer_notes,
        ])->save();
        $this->record($row, 'rejected', $reviewer, $full, ['reason' => $reasonKey]);
        $row->user->notify(new VerificationDecisionNotification($row::KIND, 'rejected', ['reason' => $full]));
    }

    public function requestInfo(IdentityVerification|CompanyVerification $row, User $reviewer, string $message): void
    {
        $this->assertReviewable($row, $reviewer);
        $row->forceFill(['status' => 'in_review', 'info_request' => $message, 'in_review_at' => $row->in_review_at ?? now()])->save();
        $this->record($row, 'info_requested', $reviewer, $message);
        $row->user->notify(new VerificationDecisionNotification($row::KIND, 'info_requested', ['message' => $message]));
    }

    public function saveNotes(IdentityVerification|CompanyVerification $row, User $reviewer, ?string $notes): void
    {
        $row->forceFill(['reviewer_notes' => $notes])->save();
        $this->record($row, 'note_saved', $reviewer);
    }

    private function assertReviewable(IdentityVerification|CompanyVerification $row, User $reviewer): void
    {
        abort_if($row->user_id === $reviewer->id, 403, 'You cannot review your own submission.');
        abort_unless($reviewer->is_admin, 403);
        abort_unless(in_array($row->status, ['submitted', 'in_review'], true), 422, 'This submission is not awaiting review.');
    }

    /**
     * Local consistency hints for the reviewer (never a decision).
     *
     * @return list<array{level: string, text: string}>
     */
    public function hints(IdentityVerification|CompanyVerification $row): array
    {
        $hints = [];
        $user = $row->user;

        if ($row instanceof IdentityVerification) {
            similar_text(self::norm((string) $row->full_name_on_document), self::norm($user->name), $pct);
            $pct = (int) round($pct);
            $hints[] = ['level' => $pct >= 85 ? 'ok' : ($pct >= 60 ? 'warn' : 'bad'),
                'text' => "Name on document vs account name \"{$user->name}\": {$pct}% similar."];

            if ($row->document_expiry !== null) {
                $days = (int) now()->startOfDay()->diffInDays($row->document_expiry, false);
                $soon = (int) config('verification.expiring_soon_days', 90);
                $hints[] = $days < 0
                    ? ['level' => 'bad', 'text' => 'Document is already expired.']
                    : ['level' => $days <= $soon ? 'warn' : 'ok', 'text' => $days <= $soon ? "Document expires in {$days} days." : 'Document is valid for '.$days.' more days.'];
            }
        } else {
            $hints[] = ['level' => 'ok', 'text' => 'Applicant: '.$user->name.' ('.$user->email.').'];
        }

        $slug = app(MemberAccess::class)->tierSlug($user);
        $hints[] = ['level' => $slug ? 'ok' : 'warn', 'text' => $slug ? 'Approved member, tier: '.str_replace('-', ' ', $slug).'.' : 'No approved membership on this email yet.'];

        if ($row instanceof IdentityVerification && $row->document_country) {
            $hints[] = ['level' => 'info', 'text' => 'Document issued in '.$row->document_country.'.'];
        }

        return $hints;
    }

    private static function norm(string $name): string
    {
        return (string) Str::of($name)->ascii()->lower()->replaceMatches('/[^a-z ]/', '')->squish();
    }

    /**
     * Retention job: purge old rejected/expired/withdrawn files, mark lapsed
     * approvals as expired, optionally strip approved images.
     *
     * @return array{purged: int, expired: int, stripped: int}
     */
    public function purge(): array
    {
        $out = ['purged' => 0, 'expired' => 0, 'stripped' => 0];
        $cutoff = now()->subDays((int) config('verification.retention_days', 30));

        foreach ([IdentityVerification::class, CompanyVerification::class] as $model) {
            $model::query()->where('status', 'approved')->whereNotNull('expires_at')->where('expires_at', '<=', now())
                ->each(function ($row) use (&$out): void {
                    $row->forceFill(['status' => 'expired'])->save();
                    $this->record($row, 'expired');
                    $out['expired']++;
                });

            $model::query()->whereIn('status', ['rejected', 'expired', 'withdrawn'])->whereNull('files_purged_at')
                ->where(fn ($q) => $q
                    ->where(fn ($a) => $a->where('status', 'expired')->where('expires_at', '<=', $cutoff))
                    ->orWhere(fn ($b) => $b->where('status', '!=', 'expired')->where(fn ($c) => $c
                        ->where('reviewed_at', '<=', $cutoff)
                        ->orWhere(fn ($d) => $d->whereNull('reviewed_at')->where('updated_at', '<=', $cutoff)))))
                ->each(function ($row) use (&$out): void {
                    $this->deleteFiles($row);
                    if ($row instanceof IdentityVerification) {
                        $row->forceFill(['date_of_birth_encrypted' => null])->save();
                    }
                    $this->record($row, 'files_purged');
                    $out['purged']++;
                });

            if (config('verification.strip_approved_files')) {
                $model::query()->where('status', 'approved')->whereNull('files_purged_at')
                    ->where('reviewed_at', '<=', now()->subDays((int) config('verification.strip_approved_after_days', 7)))
                    ->each(function ($row) use (&$out): void {
                        $this->deleteFiles($row);
                        $this->record($row, 'files_stripped');
                        $out['stripped']++;
                    });
            }
        }

        return $out;
    }
}
