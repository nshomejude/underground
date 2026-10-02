<x-admin.shell title="Plan Request" max-width="max-w-3xl">
    <div class="flex flex-col gap-6 rounded-adm border border-hairline bg-surface px-5 py-6 shadow-adm-xs sm:px-8 sm:py-8">
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div><dt class="text-[13px] text-body">Member</dt><dd class="text-cream">{{ $planRequest->user?->name }} ({{ $planRequest->user?->email }})</dd></div>
            <div><dt class="text-[13px] text-body">Received</dt><dd class="text-cream">{{ $planRequest->created_at->format('j M Y, H:i') }}</dd></div>
            <div><dt class="text-[13px] text-body">Current plan</dt><dd class="text-cream">{{ $planRequest->fromPlan?->name ?? 'Not recorded' }}</dd></div>
            <div><dt class="text-[13px] text-body">Requested plan</dt><dd class="text-cream">{{ $planRequest->toPlan->name }}</dd></div>
            <div><dt class="text-[13px] text-body">Status</dt><dd class="text-cream">{{ ucfirst($planRequest->status) }}</dd></div>
            @if ($planRequest->reviewed_at)
                <div><dt class="text-[13px] text-body">Reviewed</dt><dd class="text-cream">{{ $planRequest->reviewed_at->format('j M Y, H:i') }}{{ $planRequest->reviewer ? ' by '.$planRequest->reviewer->name : '' }}</dd></div>
            @endif
        </dl>

        <div>
            <p class="text-[13px] text-body">Member's note</p>
            <p class="text-cream">{{ $planRequest->note ?: 'No note.' }}</p>
        </div>

        @if ($planRequest->response_note)
            <div>
                <p class="text-[13px] text-body">Response sent</p>
                <p class="text-cream">{{ $planRequest->response_note }}</p>
            </div>
        @endif

        @if ($planRequest->isPending())
            <form method="POST" class="flex flex-col gap-4" id="decision">
                @csrf
                <x-admin.textarea-field name="response_note" label="Response note (included in the email to the member)" rows="4" :required="false" />
                <div class="flex flex-wrap items-center gap-4">
                    <x-button variant="primary" type="submit" formaction="{{ route('admin.plan-requests.approve', $planRequest) }}">Approve</x-button>
                    <x-button variant="secondary" type="submit" formaction="{{ route('admin.plan-requests.decline', $planRequest) }}">Decline</x-button>
                </div>
                <p class="text-xs text-body">Approving does not change the member's tier by itself: it records the decision and emails the member.</p>
            </form>
        @elseif ($planRequest->status === 'approved')
            <div class="rounded-adm border border-gold/40 bg-gold/10 p-4 text-sm text-cream" role="note">
                <strong>Next step:</strong> update the member's tier in
                <a class="text-gold underline underline-offset-4 hover:text-gold-bright" href="{{ route('admin.applications.index') }}">Membership applications</a>.
                The member's plan only changes once their membership record is updated.
            </div>
        @endif

        <a href="{{ route('admin.plan-requests.index') }}" class="text-xs font-semibold uppercase tracking-wider text-muted hover:text-cream">Back to requests</a>
    </div>
</x-admin.shell>
