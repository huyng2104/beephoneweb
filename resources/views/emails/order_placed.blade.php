<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 8px; border-top: 4px solid #f4c025; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { color: #333; margin: 0; }
        .info, .items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info td { padding: 8px 0; border-bottom: 1px solid #eee; }
        .items th, .items td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .items th { background-color: #f9f9f9; }
        .total { font-size: 18px; font-weight: bold; color: #e74c3c; text-align: right; }
        .footer { text-align: center; color: #888; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cảm ơn bạn đã đặt hàng tại Bee Phone!</h2>
            <p>Mã đơn hàng của bạn: <strong>{{ $order->order_code }}</strong></p>
        </div>

        <table class="info">
            <tr><td><strong>Người nhận:</strong></td><td>{{ $order->recipient_name }}</td></tr>
            <tr><td><strong>Số điện thoại:</strong></td><td>{{ $order->recipient_phone }}</td></tr>
            <tr><td><strong>Địa chỉ giao hàng:</strong></td><td>{{ $order->recipient_address }}</td></tr>
            <tr><td><strong>Phương thức:</strong></td><td>{{ strtoupper($order->payment_method) }}</td></tr>
        </table>

        <h3>Chi tiết đơn hàng:</h3>
        <table class="items">
            <tr>
                <th>Sản phẩm</th>
                <th>SL</th>
                <th>Thành tiền</th>
            </tr>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td>{{ number_format($item->line_total, 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </table>

        <p class="total">Tổng cộng: {{ number_format($order->total_amount, 0, ',', '.') }}đ</p>

        <div class="footer">
            <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ hotline: 1900 xxxx.</p>
            <p>Trân trọng, Đội ngũ Bee Phone.</p>
        </div>
    </div>
</body>
</html>