@if ($missing->isEmpty())
    <p class="pf-done"><x-icon name="check-circle" />Everything is filled in. Nice work.</p>
@else
    <p class="pf-miss-title">Still missing</p>
    <ul class="pf-miss">
        @foreach ($missing as $item)
            <li><a href="#{{ $item['section'] }}"><span>{{ $item['label'] }}</span><b>+{{ $item['weight'] }}%</b></a></li>
        @endforeach
    </ul>
@endif
