<x-account.shell title="Documents" active="documents">
    <header class="ac-top"><div><p class="ac-eyebrow">Member Account</p><h1>Documents</h1></div></header>
    <div class="ac-narrow">

        <div class="ac-panel ac-stack">
            <div class="ac-ph">
                <x-icon name="folder-open" class="ac-pi" />
                <h3 class="ac-h3">Your membership documents</h3>
            </div>

            <ul class="ac-list">
                @if ($certificate)
                    <li class="ac-row">
                        <span><b>Certificate of Membership</b><br>Serial {{ $certificate->serial }} &middot; valid to {{ $certificate->valid_through->format('j F Y') }}</span>
                        <a href="{{ route('account.certificate') }}" class="ac-btn ac-fit">Open <x-icon name="award" class="ac-bi" /></a>
                    </li>
                    <li class="ac-row">
                        <span><b>Welcome letter</b><br>Your personal letter from the Founder</span>
                        <a href="{{ route('account.welcome-letter') }}" class="ac-btn ac-fit">Open <x-icon name="file-text" class="ac-bi" /></a>
                    </li>
                    <li class="ac-row">
                        <span><b>Membership card</b><br>Front, back and UV view</span>
                        <a href="{{ route('account.show') }}#card" class="ac-btn ac-fit">View <x-icon name="credit-card" class="ac-bi" /></a>
                    </li>
                    <li class="ac-row">
                        <span><b>Verification link</b><br>Lets anyone confirm your credential</span>
                        <button type="button" class="ac-btn ac-fit" data-copy="{{ $certificate->verificationUrl() }}">Copy link <x-icon name="copy" class="ac-bi" /></button>
                    </li>
                @endif
                @if ($application)
                    <li class="ac-row">
                        <span><b>Application {{ $application->reference->value }}</b><br>Submitted {{ $application->submittedAt->format('j F Y') }}</span>
                        <a href="{{ route('account.applications') }}" class="ac-btn ac-fit">Open <x-icon name="file-text" class="ac-bi" /></a>
                    </li>
                @endif
                @unless ($certificate || $application)
                    <li class="ac-row"><span>You have no membership documents yet. They appear here once you apply and are approved.</span></li>
                @endunless
            </ul>
        </div>

        <div class="ac-panel ac-stack">
            <div class="ac-ph">
                <x-icon name="file-text" class="ac-pi" />
                <h3 class="ac-h3">Legal</h3>
            </div>
            <ul class="ac-list">
                <li class="ac-row"><span><b>Terms of Service</b></span><a href="{{ route('terms') }}" class="ac-btn ac-fit">Read</a></li>
                <li class="ac-row"><span><b>Privacy Policy</b></span><a href="{{ route('privacy') }}" class="ac-btn ac-fit">Read</a></li>
            </ul>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            document.querySelectorAll('[data-copy]').forEach(function (b) {
                b.addEventListener('click', function () {
                    var t = document.getElementById('ac-toast');
                    function done(ok) { t.textContent = ok ? 'Verification link copied' : 'Copy failed. Select and copy it manually.'; t.classList.add('on'); setTimeout(function () { t.classList.remove('on'); }, 2400); }
                    if (navigator.clipboard) { navigator.clipboard.writeText(b.dataset.copy).then(function () { done(true); }, function () { done(false); }); } else { done(false); }
                });
            });
        </script>
    </x-slot:scripts>
</x-account.shell>
