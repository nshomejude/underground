@props(['steps', 'current', 'route'])
<nav aria-label="Verification progress">
    <ol class="vf-steps">
        @foreach ($steps as $i => $label)
            @php $n = $i + 1; @endphp
            <li class="vf-step {{ $n < $current ? 'is-done' : '' }} {{ $n === $current ? 'is-current' : '' }}" @if ($n === $current) aria-current="step" @endif>
                @if ($n < $current)
                    <a href="{{ route($route, ['step' => $n]) }}"><b>Step {{ $n }}</b><span class="t">{{ $label }}</span><span class="sr-only">, completed</span></a>
                @else
                    <span><b>Step {{ $n }}</b><span class="t">{{ $label }}</span></span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
