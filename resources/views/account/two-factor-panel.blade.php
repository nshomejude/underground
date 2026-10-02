@php
    $user = auth()->user();
    $enabled = $user->hasTwoFactorEnabled();
@endphp

<div class="ac-panel ac-stack {{ ! $enabled && ! $setup ? 'tf-recommend' : '' }}" id="two-factor">
    <div class="ac-ph">
        <x-icon name="shield-check" class="ac-pi" />
        <h3 class="ac-h3">Two-factor authentication</h3>
    </div>

    @if (session('two_factor_required'))
        <p class="ac-flash ac-flash-warn" role="alert">{{ session('two_factor_required') }}</p>
    @endif

    <p class="tf-status {{ $enabled ? 'tf-status-on' : 'tf-status-off' }}" role="status">
        @if ($enabled)
            <x-icon name="check-circle" class="ac-bi" /> Enabled
            <span class="sr-only">since {{ $user->two_factor_confirmed_at->format('j F Y') }}</span>
        @else
            <x-icon name="x" class="ac-bi" /> Not enabled
        @endif
    </p>

    {{-- 1. Recovery codes, shown once --}}
    @if (is_array($recoveryCodes) && $recoveryCodes !== [])
        <div class="ac-stack" id="tf-recovery">
            <p class="ac-lead"><strong>Save your recovery codes now.</strong> Each works once if you lose your authenticator. They are shown only this one time and cannot be viewed again.</p>
            <ul class="tf-codes" id="tf-codes-list" aria-label="Recovery codes">
                @foreach ($recoveryCodes as $recoveryCode)
                    <li>{{ $recoveryCode }}</li>
                @endforeach
            </ul>
            <div class="tf-actions">
                <button type="button" class="ac-btn" id="tf-copy" data-codes="{{ implode("\n", $recoveryCodes) }}">Copy codes</button>
                <button type="button" class="ac-btn" id="tf-download" data-codes="{{ implode("\n", $recoveryCodes) }}">Download .txt</button>
            </div>
            <p id="tf-copy-status" class="ac-note" role="status" aria-live="polite"></p>

            <form method="POST" action="{{ route('account.two-factor.acknowledge') }}" class="ac-stack" novalidate>
                @csrf
                <label class="tf-check">
                    <input type="checkbox" name="saved" value="1" required>
                    <span>I have saved these recovery codes somewhere safe.</span>
                </label>
                @error('saved')
                    <p class="ac-err" role="alert">{{ $message }}</p>
                @enderror
                <button type="submit" class="ac-btn ac-fit">Continue</button>
            </form>
        </div>
    @endif

    {{-- 2. Pending setup: scan + confirm --}}
    @if ($setup && ! $enabled)
        <div class="ac-stack">
            <p class="ac-lead">Scan this QR code with an authenticator app (Google Authenticator, Authy, 1Password, Microsoft Authenticator), then enter the 6-digit code it shows.</p>
            <div class="tf-qr" role="img" aria-label="QR code for your authenticator app">{!! $setup['qr'] !!}</div>
            <div class="ac-field">
                <span class="ac-label" id="tf-key-label">Or enter this setup key manually</span>
                <code class="tf-key" aria-labelledby="tf-key-label">{{ $setup['key'] }}</code>
                <p class="ac-note">Time-based, 6 digits, 30 seconds. Keep this key private.</p>
            </div>

            <form method="POST" action="{{ route('account.two-factor.confirm') }}" class="ac-stack" novalidate>
                @csrf
                <div class="ac-field">
                    <label for="tf_code" class="ac-label">6-digit code <span class="text-gold" aria-hidden="true">*</span><span class="sr-only">required</span></label>
                    <input type="text" id="tf_code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]*" maxlength="7" required aria-required="true" class="ac-input tf-code-input" @if ($errors->twoFactorConfirm->has('code')) aria-invalid="true" aria-describedby="tf_code-error" @endif>
                    <div aria-live="polite">
                        @error('code', 'twoFactorConfirm')
                            <p id="tf_code-error" class="ac-err">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="tf-actions">
                    <button type="submit" class="ac-btn ac-btn-solid">Confirm and enable</button>
                </div>
            </form>
            <form method="POST" action="{{ route('account.two-factor.cancel') }}">
                @csrf
                <button type="submit" class="ac-btn ac-fit">Cancel setup</button>
            </form>
        </div>

    {{-- 3. Not enabled: recommendation + start --}}
    @elseif (! $enabled)
        <p class="ac-lead">
            @if ($twoFactorRequired)
                <strong>Required for staff administrators.</strong> You must enable two-factor authentication before using the admin area.
            @else
                <strong>Strongly recommended.</strong> A password alone can be phished or leaked. Two-factor adds a 6-digit code from your phone, so a stolen password is not enough to sign in.
            @endif
        </p>
        <form method="POST" action="{{ route('account.two-factor.start') }}" class="ac-stack" novalidate>
            @csrf
            <div class="ac-field">
                <label for="tf_start_password" class="ac-label">Confirm with your password</label>
                <input type="password" id="tf_start_password" name="current_password" required autocomplete="current-password" class="ac-input" @if ($errors->twoFactorStart->has('current_password')) aria-invalid="true" @endif>
                <div aria-live="polite">
                    @error('current_password', 'twoFactorStart')
                        <p class="ac-err">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <button type="submit" class="ac-btn ac-btn-solid ac-fit">Set up two-factor authentication</button>
        </form>

    {{-- 4. Enabled: manage --}}
    @else
        <p class="ac-lead">You will be asked for a code from your authenticator app each time you sign in. {{ $user->recoveryCodesRemaining() }} of 8 recovery codes remain.</p>

        <form method="POST" action="{{ route('account.two-factor.regenerate') }}" class="ac-stack" novalidate>
            @csrf
            <div class="ac-field">
                <label for="tf_regen_password" class="ac-label">Regenerate recovery codes: confirm with your password</label>
                <input type="password" id="tf_regen_password" name="current_password" required autocomplete="current-password" class="ac-input" @if ($errors->twoFactorRegenerate->has('current_password')) aria-invalid="true" @endif>
                <div aria-live="polite">
                    @error('current_password', 'twoFactorRegenerate')
                        <p class="ac-err">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <button type="submit" class="ac-btn ac-fit">Regenerate recovery codes</button>
        </form>

        @unless ($twoFactorRequired)
            <form method="POST" action="{{ route('account.two-factor.disable') }}" class="ac-stack" novalidate>
                @csrf
                <div class="ac-field">
                    <label for="tf_dis_password" class="ac-label">Disable: password</label>
                    <input type="password" id="tf_dis_password" name="current_password" required autocomplete="current-password" class="ac-input" @if ($errors->twoFactorDisable->has('current_password')) aria-invalid="true" @endif>
                    @error('current_password', 'twoFactorDisable')
                        <p class="ac-err">{{ $message }}</p>
                    @enderror
                </div>
                <div class="ac-field">
                    <label for="tf_dis_code" class="ac-label">Authenticator code or recovery code</label>
                    <input type="text" id="tf_dis_code" name="code" autocomplete="one-time-code" required class="ac-input" @if ($errors->twoFactorDisable->has('code')) aria-invalid="true" @endif>
                    <div aria-live="polite">
                        @error('code', 'twoFactorDisable')
                            <p class="ac-err">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="ac-btn ac-btn-danger ac-fit">Disable two-factor authentication</button>
            </form>
        @endunless
    @endif

    <script>
        (function () {
            var status = document.getElementById('tf-copy-status');
            var copy = document.getElementById('tf-copy');
            var dl = document.getElementById('tf-download');
            if (copy) copy.addEventListener('click', function () {
                var text = copy.getAttribute('data-codes');
                var done = function (m) { status.textContent = m; };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function () { done('Recovery codes copied.'); }, function () { done('Copy failed. Select the codes manually.'); });
                } else { done('Copy is not available. Select the codes manually.'); }
            });
            if (dl) dl.addEventListener('click', function () {
                var blob = new Blob(['Underground Network recovery codes\n\n' + dl.getAttribute('data-codes') + '\n\nEach code works once. Keep this file private.\n'], { type: 'text/plain' });
                var a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'underground-recovery-codes.txt';
                document.body.appendChild(a); a.click(); a.remove();
                setTimeout(function () { URL.revokeObjectURL(a.href); }, 1000);
                status.textContent = 'Recovery codes downloaded.';
            });
        })();
    </script>
</div>
