{{-- The branded error panel. Needs: $code, $headline, $message. Optional: $actions
     (list of [label, url, solid?]), $hint, $reference, $standalone. --}}
@php
    $standalone = $standalone ?? false;
    $actions = $actions ?? [];
@endphp
<section class="er" aria-labelledby="er-title">
    <p class="er-code" aria-hidden="true">{{ $code }}</p>

    <div class="er-panel">
        @if ($standalone)
            <img class="er-seal" src="/images/seal/seal-gold.png" srcset="/images/seal/seal-gold.png 1x, /images/seal/seal-gold@2x.png 2x" width="88" height="88" alt="Underground Network seal">
        @else
            <x-seal :size="88" class="er-seal" />
        @endif

        <p class="er-eyebrow">Error {{ $code }}</p>
        <h1 id="er-title" class="er-title">{{ $headline }}</h1>
        <p class="er-text">{{ $message }}</p>

        @if (! empty($hint))
            <p class="er-hint">{{ $hint }}</p>
        @endif

        @if (count($actions) > 0)
            <div class="er-actions">
                @foreach ($actions as $action)
                    <a class="er-btn {{ ($action[2] ?? false) ? 'er-btn-solid' : '' }}" href="{{ $action[1] }}">{{ $action[0] }}</a>
                @endforeach
            </div>
        @endif

        @if (! empty($reference))
            <p class="er-ref">Reference <code>{{ $reference }}</code></p>
        @endif
    </div>
</section>
