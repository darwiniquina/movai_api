<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Verify Your Email</title>
</head>

<body style="font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: #f5f5f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="padding: 30px; text-align: center;">
                            <h2 style="color: #4BB543; margin: 0;">{{ $appName }}</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 30px; color: #333;">
                            <p>Hello {{ $user->name }},</p>
                            <p>Thank you for registering at {{ $appName }}! Before you can start using your
                                account, please verify your email address.</p>

                            <p style="text-align: center; margin: 30px 0;">
                                <a href="{{ $url }}"
                                    style="display:inline-block; padding: 12px 25px; background: #4BB543; color: #fff; text-decoration: none; font-weight: bold; border-radius: 5px;">
                                    Verify Email
                                </a>
                            </p>

                            <p>This verification link will expire in 60 minutes.</p>

                            <p>If you did not create an account, no further action is required.</p>

                            <hr style="border:none; border-top:1px solid #ddd; margin: 30px 0;">

                            <p style="font-size: 14px; color: #555;">
                                If you're having trouble clicking the
                                <span style="font-weight: bold; color: #4BB543;">"Verify Email"</span>
                                button, copy and paste the URL below into your web browser:
                            </p>
                            <p style="font-size: 14px; color: #4BB543; word-break: break-all;">
                                <a href="{{ $url }}" style="color: #4BB543;">{{ $url }}</a>
                            </p>

                            <p style="margin-top: 30px;">Cheers,<br>{{ $appName }} Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
