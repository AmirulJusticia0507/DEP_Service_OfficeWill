<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OfficeWill')</title>
</head>
<body style="margin:0;padding:0;background-color:#F8FAFC;font-family:'Segoe UI',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#F8FAFC;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">

                    <tr>
                        <td style="background-color:#380812;padding:20px 32px;text-align:center;">
                            <img src="{{ config('app.url') }}/officewill_logo_yogya.svg" alt="OfficeWill" style="height:36px;width:auto;" />
                            <p style="color:#D4A017;font-size:11px;margin:4px 0 0 0;letter-spacing:1px;">DEP Service</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;color:#334155;font-size:14px;line-height:1.6;">
                            @yield('content')
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#f8fafc;padding:16px 32px;text-align:center;border-top:1px solid #e2e8f0;">
                            <p style="font-size:11px;color:#94a3b8;margin:0;">
                                OfficeWill (合同会社オフィスウィル) &bull; Yogyakarta Branch<br>
                                &copy; {{ date('Y') }} OfficeWill LLC. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
