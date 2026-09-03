<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0"
                    style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%); padding: 40px 40px 30px; text-align: center;">
                            <div
                                style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.2); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                                <span style="font-size: 28px;">🔐</span>
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">Reset Password
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #334155; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Halo <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 30px;">
                                Kami menerima permintaan untuk reset password akun InternHub Anda. Klik tombol di bawah
                                untuk membuat password baru:
                            </p>

                            <!-- Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}"
                                            style="display: inline-block; background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 12px; font-weight: 600; font-size: 15px; box-shadow: 0 4px 14px rgba(167, 139, 250, 0.4);">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <div
                                style="background-color: #fef3c7; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                                <p style="color: #92400e; font-size: 13px; line-height: 1.5; margin: 0;">
                                    ⏰ <strong>Link ini akan kadaluarsa dalam 60 menit.</strong><br>
                                    Jika Anda tidak meminta reset password, abaikan email ini.
                                </p>
                            </div>

                            <p style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 0;">
                                Jika tombol tidak berfungsi, salin dan tempel URL berikut ke browser Anda:
                            </p>
                            <p style="color: #a78bfa; font-size: 12px; word-break: break-all; margin: 8px 0 0;">
                                {{ $resetUrl }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #94a3b8; font-size: 13px; margin: 0;">
                                &copy; {{ date('Y') }} InternHub. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>