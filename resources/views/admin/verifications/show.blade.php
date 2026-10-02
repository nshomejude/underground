<x-admin.shell :title="ucfirst($kind).' verification'" max-width="max-w-4xl">
    @php($open = in_array($row->status, ['submitted', 'in_review'], true))
    <div class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-[13px] text-body">Applicant</dt><dd class="text-cream">{{ $row->user?->name }} ({{ $row->user?->email }})</dd></div>
            <div><dt class="text-[13px] text-body">Status</dt><dd class="text-cream">{{ ucfirst(str_replace('_', ' ', $row->status)) }}</dd></div>
            <div><dt class="text-[13px] text-body">Submitted</dt><dd class="text-cream">{{ $row->submitted_at?->format('j M Y, H:i') ?? 'Not submitted' }}</dd></div>
            @if ($row->reviewed_at)
                <div><dt class="text-[13px] text-body">Reviewed</dt><dd class="text-cream">{{ $row->reviewed_at->format('j M Y, H:i') }}{{ $row->reviewer ? ' by '.$row->reviewer->name : '' }}</dd></div>
            @endif
            @if ($kind === 'identity')
                <div><dt class="text-[13px] text-body">Name on document</dt><dd class="text-cream">{{ $row->full_name_on_document }}</dd></div>
                <div><dt class="text-[13px] text-body">Document</dt><dd class="text-cream">{{ ucfirst(str_replace('_', ' ', (string) $row->document_type)) }} &middot; {{ $row->document_country }} &middot; ending {{ $row->document_number_last4 }}</dd></div>
                <div><dt class="text-[13px] text-body">Date of birth</dt><dd class="text-cream">{{ $dob ?? 'Not available' }}</dd></div>
                <div><dt class="text-[13px] text-body">Document expiry</dt><dd class="text-cream">{{ $row->document_expiry?->format('j M Y') ?? 'Not given' }}</dd></div>
            @else
                <div><dt class="text-[13px] text-body">Company</dt><dd class="text-cream">{{ $row->company_name }}</dd></div>
                <div><dt class="text-[13px] text-body">Registration number</dt><dd class="text-cream">{{ $row->registration_number }}</dd></div>
                <div><dt class="text-[13px] text-body">Country</dt><dd class="text-cream">{{ $row->country }}</dd></div>
                <div><dt class="text-[13px] text-body">Incorporated</dt><dd class="text-cream">{{ $row->incorporation_date?->format('j M Y') }}</dd></div>
                <div><dt class="text-[13px] text-body">Applicant role</dt><dd class="text-cream">{{ $row->applicant_role }}</dd></div>
                <div><dt class="text-[13px] text-body">Sectors</dt><dd class="text-cream">{{ implode(', ', (array) $row->sectors) ?: 'None' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-[13px] text-body">Registered address</dt><dd class="text-cream">{{ $row->registered_address }}</dd></div>
            @endif
        </dl>

        @if ($own)
            <p class="av-note" role="note">This is your own submission. You cannot review it.</p>
        @endif

        @if ($hints !== [])
            <ul class="av-hints" aria-label="Consistency hints">
                @foreach ($hints as $hint)
                    <li class="av-hint av-hint--{{ $hint['level'] }}">{{ $hint['text'] }}</li>
                @endforeach
            </ul>
        @endif

        <section aria-labelledby="av-docs">
            <h2 id="av-docs" class="mb-2 text-sm font-semibold text-cream">Submitted documents</h2>
            @if ($files === [] || $row->files_purged_at)
                <p class="text-sm text-body">{{ $row->files_purged_at ? 'Files were removed on '.$row->files_purged_at->format('j M Y').'.' : 'No documents on file.' }}</p>
            @else
                <ul class="av-files">
                    @foreach ($files as $slot => $file)
                        <li>
                            <span class="text-cream">{{ app(\App\Services\VerificationService::class)->slotLabel($slot) }}</span>
                            <span class="text-xs text-body">{{ $file['name'] ?? $slot }}</span>
                            <a href="{{ route('verification.file', [$kind, $row->id, $slot]) }}" target="_blank" rel="noopener" class="text-xs font-semibold uppercase tracking-wider text-gold hover:text-gold-bright">View securely</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        @if ($row->info_request)
            <div><p class="text-[13px] text-body">Information requested</p><p class="text-cream">{{ $row->info_request }}</p></div>
        @endif
        @if ($row->rejection_reason)
            <div><p class="text-[13px] text-body">Rejection reason</p><p class="text-cream">{{ $row->rejection_reason }}</p></div>
        @endif

        @unless ($own)
            <form method="POST" action="{{ route('admin.verifications.notes', [$kind, $row->id]) }}" class="flex flex-col gap-3">
                @csrf
                <x-admin.textarea-field name="notes" label="Internal notes (never shown to the member)" rows="3" :required="false" :value="old('notes', $row->reviewer_notes)" />
                <div><x-button variant="secondary" type="submit">Save notes</x-button></div>
            </form>
        @endunless

        @if ($open && ! $own)
            <div class="av-actions">
                <form method="POST" action="{{ route('admin.verifications.approve', [$kind, $row->id]) }}" class="av-card">
                    @csrf
                    <h3 class="text-sm font-semibold text-cream">Approve</h3>
                    <x-admin.textarea-field name="notes" label="Note (optional)" rows="2" :required="false" />
                    <x-button variant="primary" type="submit">Approve</x-button>
                </form>

                <form method="POST" action="{{ route('admin.verifications.info', [$kind, $row->id]) }}" class="av-card">
                    @csrf
                    <h3 class="text-sm font-semibold text-cream">Request more information</h3>
                    <x-admin.textarea-field name="message" label="What is needed" rows="3" :required="true" />
                    <x-button variant="secondary" type="submit">Send request</x-button>
                </form>

                <form method="POST" action="{{ route('admin.verifications.reject', [$kind, $row->id]) }}" class="av-card">
                    @csrf
                    <h3 class="text-sm font-semibold text-cream">Reject</h3>
                    <label class="text-[13px] text-body" for="av-reason">Reason</label>
                    <select id="av-reason" name="reason" required class="av-input">
                        @foreach ($reasons as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-admin.textarea-field name="message" label="Message to member (required for Other)" rows="2" :required="false" />
                    <x-button variant="secondary" type="submit">Reject</x-button>
                </form>
            </div>
        @endif

        @if ($events->isNotEmpty())
            <section aria-labelledby="av-history">
                <h2 id="av-history" class="mb-2 text-sm font-semibold text-cream">History</h2>
                <ol class="av-events">
                    @foreach ($events as $event)
                        <li>
                            <span class="text-cream">{{ ucfirst(str_replace('_', ' ', $event->event)) }}</span>
                            <span class="text-xs text-body">{{ $event->created_at?->format('j M Y, H:i') }}{{ $event->actor ? ' by '.$event->actor->name : '' }}</span>
                            @if ($event->note)<p class="text-xs text-body">{{ $event->note }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif

        <a href="{{ route('admin.verifications.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Back to queue</a>
    </div>
</x-admin.shell>
