<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta name="color-scheme" content="dark">
    <meta name="supported-color-schemes" content="dark">
    <title>{{ $heading }}</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <style>table, td, div, p, a, h1 { font-family: Georgia, 'Times New Roman', serif !important; }</style>
    <![endif]-->
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; display: block; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #0B0B0C; }
        a { color: #E0BE7E; }
        .btn-link:hover { background-color: #E8CB8F !important; }
        @media screen and (max-width: 620px) {
            .container { width: 100% !important; max-width: 100% !important; }
            .gutter { padding-left: 12px !important; padding-right: 12px !important; }
            .frame { padding: 8px !important; }
            .px { padding-left: 24px !important; padding-right: 24px !important; }
            .h1 { font-size: 29px !important; line-height: 36px !important; }
            .btn-wrap { width: 100% !important; }
            .btn-link { display: block !important; padding-left: 16px !important; padding-right: 16px !important; }
            .stack { display: block !important; width: 100% !important; }
            .stack-gap { padding-bottom: 14px !important; }
            .val { text-align: left !important; padding-top: 4px; }
            .hide-m { display: none !important; max-height: 0 !important; overflow: hidden !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#0B0B0C;">
    {{-- Inbox preview text --}}
    <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">
        {{ $preheader ?? $heading }}
        &#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#0B0B0C" style="background-color:#0B0B0C;">
        <tr>
            <td align="center" class="gutter" style="padding:40px 20px 48px 20px;">

                <!--[if mso]><table role="presentation" width="600" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;">

                    {{-- ============ MASTHEAD ============ --}}
                    <tr>
                        <td align="center" style="padding:0 0 28px 0;">
                            <img src="{{ asset('images/email/seal-gold@2x.png') }}" width="120" height="120" alt="Underground Network seal" style="width:120px;height:120px;margin:0 auto;">
                            <div style="padding-top:18px;font-family:Georgia,'Times New Roman',serif;font-size:22px;line-height:26px;font-weight:bold;letter-spacing:7px;color:#F3EFE6;text-align:center;">UNDERGROUND</div>
                            <div style="padding-top:8px;font-family:Helvetica,Arial,sans-serif;font-size:11px;line-height:16px;letter-spacing:4px;text-transform:uppercase;color:#A39E94;text-align:center;">Power beneath the surface</div>
                        </td>
                    </tr>

                    {{-- ============ FRAMED LETTER ============ --}}
                    <tr>
                        <td bgcolor="#141317" style="background-color:#141317;border:1px solid #34301F;border-top:4px solid #C9A25A;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td class="frame" style="padding:12px;">
                                        {{-- inner hairline frame --}}
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #3A3326;">
                                            <tr>
                                                <td class="px" style="padding:44px 48px 40px 48px;">

                                                    {{-- eyebrow with hairlines --}}
                                                    @if (! empty($eyebrow))
                                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                            <tr>
                                                                <td width="22%" style="border-bottom:1px solid #4A3E22;font-size:1px;line-height:1px;">&nbsp;</td>
                                                                <td align="center" style="padding:0 14px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:16px;font-weight:bold;letter-spacing:4px;text-transform:uppercase;color:#C9A25A;white-space:nowrap;">{{ $eyebrow }}</td>
                                                                <td width="22%" style="border-bottom:1px solid #4A3E22;font-size:1px;line-height:1px;">&nbsp;</td>
                                                            </tr>
                                                        </table>
                                                    @endif

                                                    {{-- headline --}}
                                                    <h1 class="h1" style="margin:26px 0 0 0;font-family:Georgia,'Times New Roman',serif;font-size:36px;line-height:44px;font-weight:normal;color:#F3EFE6;text-align:center;">{{ $heading }}@if (! empty($headingEm)) <em style="color:#E0BE7E;font-style:italic;">{{ $headingEm }}</em>@endif</h1>

                                                    {{-- diamond divider --}}
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:24px;">
                                                        <tr>
                                                            <td width="40%" style="border-bottom:1px solid #3A3326;font-size:1px;line-height:1px;">&nbsp;</td>
                                                            <td align="center" style="padding:0 12px;font-family:Georgia,serif;font-size:12px;line-height:12px;color:#C9A25A;">&#9670;</td>
                                                            <td width="40%" style="border-bottom:1px solid #3A3326;font-size:1px;line-height:1px;">&nbsp;</td>
                                                        </tr>
                                                    </table>

                                                    @yield('content')

                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ============ FOOTER ============ --}}
                    <tr>
                        <td align="center" style="padding:36px 16px 0 16px;">
                            <img src="{{ asset('images/email/seal-gold.png') }}" width="56" height="56" alt="" style="width:56px;height:56px;margin:0 auto;">
                            <div style="padding-top:18px;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:24px;color:#A39E94;text-align:center;">
                                <a href="{{ url('/') }}" style="color:#D9D3C7;text-decoration:underline;">un-der.com</a>
                                &nbsp;&middot;&nbsp;
                                <a href="mailto:info@un-der.com" style="color:#D9D3C7;text-decoration:underline;">info@un-der.com</a>
                                &nbsp;&middot;&nbsp;
                                <a href="{{ route('privacy') }}" style="color:#D9D3C7;text-decoration:underline;">Privacy</a>
                            </div>
                            <div style="padding-top:10px;font-family:Helvetica,Arial,sans-serif;font-size:13px;line-height:21px;color:#A39E94;text-align:center;">
                                {{ $footerNote ?? 'This is a message about your account at Underground Network.' }}<br>
                                200 Massachusetts Ave NW, Washington, DC 20001
                            </div>
                            <div style="padding-top:14px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:18px;letter-spacing:2px;text-transform:uppercase;color:#8E897F;text-align:center;">
                                &copy; {{ now()->year }} {{ $appName }} Inc. &middot; Powered by <a href="https://opesware.com" style="color:#B5AFA3;text-decoration:none;">opesware.com</a>
                            </div>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->

            </td>
        </tr>
    </table>
</body>
</html>
