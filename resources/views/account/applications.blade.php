<x-account.shell title="Applications" active="applications">
    <header class="ac-top"><div><p class="ac-eyebrow">Member Account</p><h1>Applications</h1></div></header>
    <div class="ac-narrow">

        @if ($application === null)
            <div class="ac-panel ac-stack">
                <div class="ac-ph">
                    <x-icon name="file-text" class="ac-pi" />
                    <h3 class="ac-h3">No application yet</h3>
                </div>
                <p class="ac-lead">You have not applied for membership. Every application is reviewed personally by a partner.</p>
                <a href="{{ route('membership.index') }}" class="ac-btn ac-btn-solid ac-fit">
                    Explore Membership
                    <x-icon name="chevron-right" class="ac-bi" />
                </a>
            </div>
        @else
            <div class="ac-panel ac-stack">
                <div class="ac-ph">
                    <x-icon name="file-text" class="ac-pi" />
                    <h3 class="ac-h3">{{ $tier?->name ?? 'Membership' }} application</h3>
                </div>

                <ul class="ac-list">
                    <li class="ac-row"><span>Reference</span><b class="ac-ref">{{ $application->reference->value }}</b></li>
                    <li class="ac-row"><span>Submitted</span><b>{{ $application->submittedAt->format('j F Y') }}</b></li>
                    <li class="ac-row"><span>Status</span><x-status-badge :label="$application->status()->label()" :tone="$application->status()->value === 'approved' ? 'success' : ($application->status()->value === 'declined' ? 'danger' : 'info')" /></li>
                    @if ($application->memberId())
                        <li class="ac-row"><span>Member ID</span><b class="ac-ref">{{ $application->memberId() }}</b></li>
                    @endif
                </ul>

                <div class="ac-chips">
                    <a href="{{ route('membership.track', ['reference' => $application->reference->value]) }}" class="ac-btn ac-fit">
                        Track This Application
                        <x-icon name="chevron-right" class="ac-bi" />
                    </a>
                    @if ($certificate)
                        <a href="{{ route('account.certificate') }}" class="ac-btn ac-btn-solid ac-fit">
                            View Certificate
                            <x-icon name="award" class="ac-bi" />
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-account.shell>
