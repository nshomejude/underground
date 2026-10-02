UNDERGROUND — Power beneath the surface

{{ strtoupper($heading) }}

@foreach ($intro as $line)
{{ $line }}

@endforeach
@if (! empty($actionUrl))
{{ $actionText }}:
{{ $actionUrl }}

@endif
@foreach ($outro as $line)
{{ $line }}

@endforeach
With discretion,
The {{ $appName }} team

--
Questions? Write to info@un-der.com
(c) {{ now()->year }} {{ $appName }} Inc. | Powered by opesware.com
