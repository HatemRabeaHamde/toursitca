<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name') }}</title>
    </head>
    <body style="margin:0;background:#f8fafc;color:#0f172a;font-family:Arial,sans-serif;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f8fafc;padding:24px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;">
                        <tr>
                            <td style="padding:24px;font-size:18px;font-weight:700;">{{ __('ui.brand') }}</td>
                        </tr>
                        <tr>
                            <td style="padding:0 24px 24px 24px;font-size:14px;line-height:1.6;">
                                {{ $slot ?? '' }}
                                @yield('content')
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top:1px solid #e2e8f0;padding:16px 24px;color:#64748b;font-size:12px;">
                                {{ __('emails.footer') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
