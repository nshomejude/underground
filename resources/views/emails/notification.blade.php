@extends('emails.layout')

@section('content')
    {{-- intro --}}
    @foreach ($intro as $line)
        <p style="margin:{{ $loop->first ? '26px' : '16px' }} 0 0 0;font-family:Helvetica,Arial,sans-serif;font-size:17px;line-height:28px;color:#D2CCC0;text-align:center;">{{ $line }}</p>
    @endforeach

    {{-- credential summary --}}
    @if (! empty($summary))
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:30px;background-color:#1B1A1F;border:1px solid #3A3326;">
            @foreach ($summary as $row)
                <tr>
                    <td style="padding:16px 22px;{{ ! $loop->last ? 'border-bottom:1px solid #2F2B22;' : '' }}">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td class="stack" valign="middle" style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:18px;letter-spacing:2px;text-transform:uppercase;color:#C9B27E;">{{ $row[0] }}</td>
                                <td class="stack val" valign="middle" align="right" style="font-family:{{ ! empty($row[2]) ? "'Courier New',Courier,monospace" : 'Helvetica,Arial,sans-serif' }};font-size:16px;line-height:24px;font-weight:bold;color:#F3EFE6;text-align:right;">{{ $row[1] }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- action --}}
    @if (! empty($actionUrl))
        <div style="padding-top:34px;">
            @include('emails.partials.button', ['url' => $actionUrl, 'label' => $actionText])
        </div>
        <p style="margin:22px 0 0 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;line-height:21px;color:#A39E94;text-align:center;word-break:break-all;">
            If the button does not work, paste this link into your browser:<br>
            <a href="{{ $actionUrl }}" style="color:#D9D3C7;">{{ $actionUrl }}</a>
        </p>
    @endif

    {{-- steps --}}
    @if (! empty($steps))
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:34px;">
            <tr>
                <td style="padding-bottom:6px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:16px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;color:#C9A25A;">Where to begin</td>
            </tr>
            @foreach ($steps as $step)
                <tr>
                    <td style="padding:16px 0;{{ ! $loop->last ? 'border-bottom:1px solid #2F2B22;' : '' }}">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="48" valign="top" style="width:48px;font-family:Georgia,serif;font-size:26px;line-height:28px;color:#C9A25A;">0{{ $loop->iteration }}</td>
                                <td valign="top" style="font-family:Helvetica,Arial,sans-serif;font-size:16px;line-height:25px;color:#D2CCC0;"><strong style="color:#F3EFE6;">{{ $step[0] }}.</strong> {{ $step[1] }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- outro --}}
    @foreach ($outro as $line)
        <p style="margin:{{ $loop->first ? '28px' : '14px' }} 0 0 0;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:25px;color:#B9B4AC;text-align:center;">{{ $line }}</p>
    @endforeach

    {{-- sign-off --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:34px;">
        <tr>
            <td align="center" style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:24px;color:#B9B4AC;">
                With discretion,<br>
                @if (! empty($signedBy))
                    <span style="display:inline-block;padding-top:8px;font-family:Georgia,serif;font-size:24px;line-height:30px;font-style:italic;color:#F3EFE6;">{{ $signedBy }}</span><br>
                    <span style="font-size:13px;line-height:20px;letter-spacing:2px;text-transform:uppercase;color:#A39E94;">{{ $signedTitle ?? '' }}</span>
                @else
                    <span style="display:inline-block;padding-top:6px;font-family:Georgia,serif;font-size:19px;line-height:26px;color:#F3EFE6;">The {{ $appName }} team</span>
                @endif
            </td>
        </tr>
    </table>
@endsection
