<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Xác thực mã OTP - Bee Phone</title>
</head>

<body
    style="margin: 0; padding: 20px; background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table cellpadding="0" cellspacing="0" width="100%"
        style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <tr>
            <td style="background-color: #f4c025; padding: 40px; text-align: center;">
                <div
                    style="display: inline-block; background-color: #1a1a1a; color: #f4c025; font-weight: bold; font-size: 28px; padding: 10px 25px; border-radius: 8px; font-style: italic;">
                    BEE PHONE
                </div>
            </td>
        </tr>

        <tr>
            <td style="padding: 40px; color: #374151; line-height: 1.6;">
                <h1 style="font-size: 24px; color: #1a1a1a; margin-bottom: 20px;">Xin chào quý khách,{{$email}}</h1>
                <p style="margin-bottom: 25px;">
                    Bạn đã yêu cầu mã xác nhận để <strong>đổi mật khẩu</strong> tại hệ thống của <strong>Bee
                        Phone</strong>.
                </p>

                <div
                    style="border: 2px dashed #f4c025; background-color: #fffbeb; border-radius: 8px; padding: 30px; text-align: center; margin-bottom: 30px;">
                    <span style="font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 2px;">Mã
                        xác nhận của bạn</span>
                    <div
                        style="font-size: 40px; font-weight: 900; color: #1a1a1a; letter-spacing: 8px; margin-top: 10px;">
                        {{ $code }}
                    </div>
                </div>

                <div
                    style="border-left: 4px solid #f4c025; background-color: #f9fafb; padding: 15px; margin-bottom: 20px; font-size: 14px;">
                    Mã này có hiệu lực trong <strong>5 phút</strong>. Vui lòng <span
                        style="color: #dc2626; font-weight: bold;">không chia sẻ</span> mã này cho bất kỳ ai.
                </div>
            </td>
        </tr>

        <tr>
            <td style="background-color: #1a1a1a; color: #9ca3af; padding: 30px; font-size: 13px;">
                <table width="100%">
                    <tr>
                        <td width="50%" style="vertical-align: top;">
                            <h3
                                style="color: #f4c025; font-size: 14px; text-transform: uppercase; margin-bottom: 10px;">
                                Liên hệ</h3>
                            Hotline: 1900 1234<br>
                            Website: www.beephone.vn
                        </td>
                        <td width="50%" style="text-align: right; vertical-align: top;">
                            <h3
                                style="color: #f4c025; font-size: 14px; text-transform: uppercase; margin-bottom: 10px;">
                                Bee Phone</h3>
                            "Kết nối đam mê,<br>Nâng tầm công nghệ"
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
