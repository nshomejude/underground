{{-- Bulletproof button: real padding (not line-height), VML for Outlook, full width on phones. --}}
<table role="presentation" class="btn-wrap" align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
    <tr>
        <td align="center">
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $url }}" style="height:54px;v-text-anchor:middle;width:300px;" arcsize="0%" strokecolor="#E0BE7E" strokeweight="1px" fillcolor="#C9A25A">
                <w:anchorlock/>
                <center style="color:#16140F;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;letter-spacing:2px;">{{ strtoupper($label) }}</center>
            </v:roundrect>
            <![endif]-->
            <!--[if !mso]><!-->
            <table role="presentation" class="btn-wrap" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" bgcolor="#C9A25A" style="background-color:#C9A25A;border:1px solid #E0BE7E;">
                        <a class="btn-link" href="{{ $url }}" target="_blank" style="display:inline-block;padding:18px 44px;background-color:#C9A25A;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:18px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;text-decoration:none;color:#16140F;text-align:center;mso-hide:all;">{{ $label }}</a>
                    </td>
                </tr>
            </table>
            <!--<![endif]-->
        </td>
    </tr>
</table>
