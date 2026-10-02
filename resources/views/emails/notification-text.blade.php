UNDERGROUND — Power beneath the surface

{{ strtoupper($heading.(! empty($headingEm) ? ' '.$headingEm : '')) }}

@foreach ($intro as $line)
{{ $line }}

@endforeach
@foreach ($summary as $row)
{{ $row[0] }}: {{ $row[1] }}
@endforeach
@if (! empty($summary))

@endif
@if (! empty($actionUrl))
{{ $actionText }}:
{{ $actionUrl }}

@endif
@if (! empty($steps))
WHERE TO BEGIN
@foreach ($steps as $step)
{{ $loop->iteration }}. {{ $step[0] }}. {{ $step[1] }}
@endforeach

@endif
@foreach ($outro as $line)
{{ $line }}

@endforeach
With discretion,
@if (! empty($signedBy))
{{ $signedBy }}, {{ $signedTitle }}
@else
The {{ $appName }} team
@endif

--
Questions? Write to info@un-der.com
200 Massachusetts Ave NW, Washington, DC 20001
(c) {{ now()->year }} {{ $appName }} Inc. | Powered by opesware.com
