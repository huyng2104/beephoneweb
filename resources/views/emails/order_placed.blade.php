<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng Bee Phone</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f8f5; font-family: Helvetica, Arial, sans-serif;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8f8f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e6e3db;">
                    
                    <tr>
                        <td align="center" style="background-color: #f4c025; padding: 40px 20px;">
                            <div style="background-color: #ffffff; width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 15px auto; line-height: 60px;">
                                <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" width="30" height="30" alt="Success" style="vertical-align: middle; display: inline-block; margin-top: 15px;">
                            </div>
                            <h1 style="margin: 0; color: #181611; font-size: 28px; font-weight: bold;">Đặt hàng thành công!</h1>
                            <p style="margin: 8px 0 0 0; color: #181611; font-size: 16px;">Cảm ơn bạn đã tin tưởng Bee Phone</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 40px 20px 40px;">
                            <h2 style="margin: 0 0 10px 0; color: #181611; font-size: 20px;">Chào {{ $order->customer_name }},</h2>
                            <p style="margin: 0 0 30px 0; color: #555555; font-size: 15px; line-height: 1.6;">
                                Đơn hàng <strong style="color: #181611;">#{{ $order->order_code }}</strong> của bạn đã được hệ thống ghi nhận thành công và đang trong quá trình chuẩn bị để giao đi.
                            </p>

                            <div style="background-color: #f8f8f5; border: 1px solid #e6e3db; border-radius: 8px; padding: 25px;">
                                <h3 style="margin: 0 0 15px 0; color: #181611; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Thông tin đơn hàng</h3>
                                
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #e6e3db;">
                                            <p style="margin: 0; color: #181611; font-weight: bold; font-size: 15px;">{{ $item->product_name }}</p>
                                            <p style="margin: 5px 0 0 0; color: #8a8060; font-size: 13px;">Số lượng: {{ $item->quantity }}</p>
                                        </td>
                                        <td align="right" style="padding: 10px 0; border-bottom: 1px solid #e6e3db; color: #181611; font-weight: bold;">
                                            {{ number_format($item->line_total, 0, ',', '.') }}đ
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>

                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 15px;">
                                    <tr>
                                        <td style="padding: 5px 0; color: #8a8060; font-size: 14px;">Tạm tính:</td>
                                        <td align="right" style="padding: 5px 0; color: #181611; font-size: 14px; font-weight: bold;">{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #8a8060; font-size: 14px;">Phí vận chuyển:</td>
                                        <td align="right" style="padding: 5px 0; color: #181611; font-size: 14px; font-weight: bold;">Miễn phí</td>
                                    </tr>
                                    
                                    @if($order->total_price > $order->total_amount)
                                    <tr>
                                        <td style="padding: 5px 0; color: #27ae60; font-size: 14px;">Giảm giá Voucher:</td>
                                        <td align="right" style="padding: 5px 0; color: #27ae60; font-size: 14px; font-weight: bold;">-{{ number_format($order->total_price - $order->total_amount, 0, ',', '.') }}đ</td>
                                    </tr>
                                    @endif

                                    <tr>
                                        <td style="padding: 20px 0 0 0; border-top: 1px solid #e6e3db; color: #181611; font-size: 16px; font-weight: bold;">Tổng thanh toán:</td>
                                        <td align="right" style="padding: 20px 0 0 0; border-top: 1px solid #e6e3db; color: #d35400; font-size: 20px; font-weight: bold;">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="48%" valign="top" style="background-color: #f8f8f5; border-radius: 8px; padding: 20px; border: 1px solid #e6e3db;">
                                        <p style="margin: 0 0 10px 0; color: #8a8060; font-size: 12px; text-transform: uppercase; font-weight: bold;">Người nhận</p>
                                        <p style="margin: 0; color: #181611; font-size: 15px; font-weight: bold;">{{ $order->recipient_name }}</p>
                                        <p style="margin: 5px 0 0 0; color: #555555; font-size: 14px;">{{ $order->recipient_phone }}</p>
                                    </td>
                                    <td width="4%"></td>
                                    <td width="48%" valign="top" style="background-color: #f8f8f5; border-radius: 8px; padding: 20px; border: 1px solid #e6e3db;">
                                        <p style="margin: 0 0 10px 0; color: #8a8060; font-size: 12px; text-transform: uppercase; font-weight: bold;">Giao đến</p>
                                        <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.5;">{{ $order->recipient_address }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 0 40px 40px 40px;">
                            <a href="{{ route('home') }}" style="background-color: #f4c025; color: #181611; text-decoration: none; padding: 15px 35px; border-radius: 30px; font-size: 16px; font-weight: bold; display: inline-block;">Tra cứu đơn hàng trên Web</a>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #181611; padding: 30px 20px;">
                            <p style="margin: 0; color: #ffffff; font-size: 18px; font-weight: bold;">Bee Phone Store</p>
                            <p style="margin: 10px 0 0 0; color: #888888; font-size: 13px;">Nếu bạn có thắc mắc, vui lòng liên hệ hotline: <strong style="color: #f4c025;">1900 6789</strong></p>
                            <p style="margin: 5px 0 0 0; color: #888888; font-size: 13px;">care@beephone.vn | www.beephone.vn</p>
                        </td>
                    </tr>
                </table>
                <p style="margin-top: 20px; color: #aaaaaa; font-size: 12px;">Email này được gửi tự động. Vui lòng không trả lời.</p>
            </td>
        </tr>
    </table>
</body>
</html>