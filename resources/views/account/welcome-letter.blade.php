@php
    $first = trim(explode(' ', trim($addressee))[0]);
@endphp

<x-account.shell title="Welcome letter" active="documents">
    <header class="ac-top ltr-hide">
        <div>
            <p class="ac-eyebrow">Member Account</p>
            <h1>Welcome letter</h1>
        </div>
        <button type="button" class="ac-btn ac-btn-solid ac-fit" id="ltr-print">
            Print or save as PDF
            <x-icon name="download" class="ac-bi" />
        </button>
    </header>

    <div class="ltr-stage">
        <article class="letter" aria-label="Welcome letter">
            <div class="ltr-paper">
                <header class="ltr-head">
                    <div class="ltr-brand">
                        <x-seal :size="76" />
                        <div>
                            <b>UNDERGROUND</b>
                            <small>Power beneath the surface</small>
                        </div>
                    </div>
                    <div class="ltr-meta"><b>Office of the Founder</b><br>Washington, D.C.</div>
                </header>

                <div class="ltr-date">{{ $issuedAt->format('j F Y') }}</div>
                <div class="ltr-to"><b>{{ $holder }}</b><br>{{ $tierName }} {{ $isOrganisation ? 'affiliate' : 'member' }}</div>

                <h1 class="ltr-h1">Welcome to Underground, <em>{{ $first }}.</em></h1>
                <p>Dear {{ $first }},</p>

                <div class="ltr-cols">
                    <div class="ltr-text">
                        <p>It is a pleasure to confirm your membership of the {{ $tierName }}. Our tiers are extended to a deliberately small number of principals and institutions, and your application was reviewed personally by a partner.</p>
                        <p>Membership gives you a private channel to the firm, early sight of our analysis and priority consideration for our closed forums. Everything you share with us stays with us.</p>
                        <p>Your membership card and certificate are ready in your member area. Anyone can confirm their authenticity by scanning the QR code printed on each.</p>
                    </div>

                    <div class="ltr-side">
                        <div class="ltr-card" role="group" aria-label="Your membership at a glance">
                            <div><span>Tier</span><b>{{ $tierName }}</b></div>
                            <div><span>Valid through</span><b>{{ $validThrough->format('j M Y') }}</b></div>
                            <div class="full"><span>Member ID</span><b class="mono">{{ $memberId }}</b></div>
                            <div class="full"><span>Certificate serial</span><b class="mono">{{ $serial }}</b></div>
                        </div>

                        <h2 class="ltr-h2">Your first steps</h2>
                        <ol class="ltr-steps">
                            <li><i>1</i><p><b>Secure your account.</b> Verify your email and turn on two-step sign-in.</p></li>
                            <li><i>2</i><p><b>Open your card</b> and add the member area to your phone's home screen.</p></li>
                            <li><i>3</i><p><b>Write to us</b> through a confidential inquiry whenever you need us.</p></li>
                        </ol>
                    </div>
                </div>

                <p>We look forward to being of service, quietly and well.</p>

                <div class="ltr-sign">
                    <p>With discretion,</p>
                    <b class="ltr-name">Tony Smith</b>
                    <small>Founder &amp; Managing Partner</small>
                </div>

                <footer class="ltr-foot">
                    <x-seal :size="52" variant="ink" />
                    <span>Underground Network Inc. &middot; 200 Massachusetts Ave NW, Washington, DC 20001<br>info@un-der.com &middot; +1-571-508-9170 &middot; un-der.com</span>
                </footer>
            </div>
        </article>
    </div>

    <x-slot:scripts>
        <script>
            var p = document.getElementById('ltr-print');
            if (p) { p.addEventListener('click', function () { window.print(); }); }
        </script>
    </x-slot:scripts>
</x-account.shell>
