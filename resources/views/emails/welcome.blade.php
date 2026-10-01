<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta name="color-scheme" content="dark light">
    <meta name="supported-color-schemes" content="dark light">
    <title>Welcome to {{ $appName }}</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <style>table, td, div, p, a { font-family: Georgia, 'Times New Roman', serif !important; }</style>
    <![endif]-->
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #0B0B0C; }
        a { color: #C9A25A; }
        .btn:hover { background-color: #E0BE7E !important; border-color: #E0BE7E !important; }
        @media screen and (max-width: 620px) {
            .container { width: 100% !important; }
            .px { padding-left: 24px !important; padding-right: 24px !important; }
            .h1 { font-size: 28px !important; line-height: 36px !important; }
            .stack { display: block !important; width: 100% !important; }
            .stack-pad { padding: 0 0 20px 0 !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#0B0B0C;">
    {{-- Preheader: shown in the inbox preview, hidden in the body --}}
    <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">
        Your account is ready. Power, calm and connection &mdash; begin below.
        &#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#0B0B0C" style="background-color:#0B0B0C;">
        <tr>
            <td align="center" style="padding:32px 12px;">

                <!--[if mso]><table role="presentation" width="600" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;">

                    {{-- Brand mark --}}
                    <tr>
                        <td align="center" style="padding:0 0 28px 0;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="40" height="40" align="center" valign="middle" style="width:40px;height:40px;border:1px solid #C9A25A;font-family:Georgia,'Times New Roman',serif;font-size:20px;font-weight:bold;color:#C9A25A;line-height:40px;">U</td>
                                    <td style="padding-left:14px;text-align:left;">
                                        <div style="font-family:Georgia,'Times New Roman',serif;font-size:18px;font-weight:bold;letter-spacing:2px;color:#F3EFE6;line-height:20px;">UNDERGROUND</div>
                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:9px;letter-spacing:3px;color:#7A756C;line-height:16px;text-transform:uppercase;">Power beneath the surface</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td bgcolor="#17161A" style="background-color:#17161A;border:1px solid #2A2825;border-top:3px solid #C9A25A;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

                                {{-- Hero --}}
                                <tr>
                                    <td class="px" align="center" style="padding:52px 48px 8px 48px;">
                                        <div style="font-family:Helvetica,Arial,sans-serif;font-size:11px;font-weight:bold;letter-spacing:4px;text-transform:uppercase;color:#C9A25A;line-height:16px;">Welcome</div>
                                        <h1 class="h1" style="margin:18px 0 0 0;font-family:Georgia,'Times New Roman',serif;font-size:34px;line-height:42px;font-weight:normal;color:#F3EFE6;">
                                            {{ $firstName }}, you&rsquo;re in.
                                        </h1>
                                    </td>
                                </tr>

                                {{-- Divider --}}
                                <tr>
                                    <td align="center" style="padding:24px 0 0 0;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr><td width="48" height="1" style="width:48px;height:1px;line-height:1px;font-size:1px;background-color:#C9A25A;">&nbsp;</td></tr>
                                        </table>
                                    </td>
                                </tr>

                                {{-- Intro --}}
                                <tr>
                                    <td class="px" style="padding:28px 48px 8px 48px;font-family:Helvetica,Arial,sans-serif;font-size:16px;line-height:26px;color:#B9B4AC;text-align:center;">
                                        Thank you for creating your {{ $appName }} account. We are a global network built on quiet influence and trusted connections &mdash; and we&rsquo;re glad to have you among us.
                                    </td>
                                </tr>

                                {{-- CTA --}}
                                <tr>
                                    <td align="center" style="padding:28px 48px 12px 48px;">
                                        <!--[if mso]>
                                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $accountUrl }}" style="height:48px;v-text-anchor:middle;width:240px;" arcsize="0%" strokecolor="#C9A25A" fillcolor="#C9A25A">
                                            <w:anchorlock/>
                                            <center style="color:#0B0B0C;font-family:Arial,sans-serif;font-size:13px;font-weight:bold;letter-spacing:2px;">OPEN YOUR ACCOUNT</center>
                                        </v:roundrect>
                                        <![endif]-->
                                        <!--[if !mso]><!-->
                                        <a class="btn" href="{{ $accountUrl }}" target="_blank" style="display:inline-block;background-color:#C9A25A;border:1px solid #C9A25A;color:#0B0B0C;font-family:Helvetica,Arial,sans-serif;font-size:13px;font-weight:bold;letter-spacing:2px;line-height:48px;text-align:center;text-decoration:none;text-transform:uppercase;padding:0 36px;mso-hide:all;">Open your account</a>
                                        <!--<![endif]-->
                                    </td>
                                </tr>

                                {{-- Quote --}}
                                <tr>
                                    <td class="px" align="center" style="padding:36px 48px 8px 48px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #2A2825;border-bottom:1px solid #2A2825;">
                                            <tr>
                                                <td align="center" style="padding:28px 8px;font-family:Georgia,'Times New Roman',serif;font-size:20px;line-height:30px;font-style:italic;color:#E0BE7E;">
                                                    &ldquo;The strongest rooms are quiet. The decisions made inside them are not.&rdquo;
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                {{-- What happens next --}}
                                <tr>
                                    <td class="px" style="padding:36px 48px 4px 48px;font-family:Helvetica,Arial,sans-serif;font-size:11px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;color:#7A756C;">
                                        Where to begin
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px" style="padding:8px 48px 0 48px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="36" valign="top" style="width:36px;padding:16px 0;font-family:Georgia,'Times New Roman',serif;font-size:20px;color:#C9A25A;border-bottom:1px solid #2A2825;">01</td>
                                                <td valign="top" style="padding:16px 0;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;color:#B9B4AC;border-bottom:1px solid #2A2825;">
                                                    <strong style="color:#F3EFE6;">Verify your email.</strong> A separate message carries your confirmation link &mdash; it keeps your account secure.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="36" valign="top" style="width:36px;padding:16px 0;font-family:Georgia,'Times New Roman',serif;font-size:20px;color:#C9A25A;border-bottom:1px solid #2A2825;">02</td>
                                                <td valign="top" style="padding:16px 0;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;color:#B9B4AC;border-bottom:1px solid #2A2825;">
                                                    <strong style="color:#F3EFE6;">Explore membership.</strong> Our tiers are extended to a vetted few. <a href="{{ $membershipUrl }}" style="color:#C9A25A;text-decoration:underline;">Review the tiers</a> and apply when ready.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="36" valign="top" style="width:36px;padding:16px 0;font-family:Georgia,'Times New Roman',serif;font-size:20px;color:#C9A25A;">03</td>
                                                <td valign="top" style="padding:16px 0;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:23px;color:#B9B4AC;">
                                                    <strong style="color:#F3EFE6;">Speak with us, in confidence.</strong> Every inquiry is handled discreetly. <a href="{{ $contactUrl }}" style="color:#C9A25A;text-decoration:underline;">Get in touch</a>.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                {{-- Sign-off --}}
                                <tr>
                                    <td class="px" style="padding:32px 48px 52px 48px;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:24px;color:#B9B4AC;">
                                        With discretion,<br>
                                        <span style="font-family:Georgia,'Times New Roman',serif;font-size:17px;color:#F3EFE6;">The {{ $appName }} team</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="padding:28px 24px 8px 24px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:20px;color:#7A756C;">
                            You received this message because an account was created at
                            <a href="{{ $siteUrl }}" style="color:#B9B4AC;text-decoration:underline;">un-der.com</a> with this email address.
                            If this wasn&rsquo;t you, you can safely ignore it or reply to let us know.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:12px 24px 0 24px;font-family:Helvetica,Arial,sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#7A756C;line-height:18px;">
                            &copy; {{ now()->year }} {{ $appName }} Inc.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:6px 24px 0 24px;font-family:Helvetica,Arial,sans-serif;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#7A756C;line-height:18px;">
                            Powered by <a href="https://opesware.com" style="color:#B9B4AC;text-decoration:none;">opesware.com</a>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->

            </td>
        </tr>
    </table>
</body>
</html>
