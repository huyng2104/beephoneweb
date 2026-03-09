<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đơn hàng {{ $order->order_code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #111827;
            margin: 24px;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .meta {
            color: #4b5563;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f9fafb;
            width: 28%;
        }

        .total {
            font-size: 18px;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">PHIẾU ĐƠN HÀNG</div>
        <div class="meta">Mã đơn: {{ $order->order_code }} | Ngày in: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <tr>
            <th>Người đặt hàng</th>
            <td>{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <th>SĐT người đặt</th>
            <td>{{ $order->customer_phone }}</td>
        </tr>
        <tr>
            <th>Email người đặt</th>
            <td>{{ $order->customer_email ?: 'Chưa có' }}</td>
        </tr>
        <tr>
            <th>Người nhận hàng</th>
            <td>{{ $order->recipient_name ?: $order->customer_name }}</td>
        </tr>
        <tr>
            <th>SĐT người nhận</th>
            <td>{{ $order->recipient_phone ?: $order->customer_phone }}</td>
        </tr>
        <tr>
            <th>Địa chỉ nhận hàng</th>
            <td>{{ $order->recipient_address ?: $order->shipping_address ?: 'Chưa có' }}</td>
        </tr>
        <tr>
            <th>Ngày đặt</th>
            <td>{{ optional($order->ordered_at)->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <th>Trạng thái đơn</th>
            <td>{{ $statusLabels[$order->status] ?? $order->status }}</td>
        </tr>
        <tr>
            <th>Đổi/Trả</th>
            <td>{{ $returnStatusLabels[$order->return_status] ?? $order->return_status }}</td>
        </tr>
        <tr>
            <th>Ghi chú</th>
            <td>{{ $order->note ?: 'Không có' }}</td>
        </tr>
        <tr>
            <th>Lý do hủy</th>
            <td>{{ $order->cancellation_reason ?: 'Không có' }}</td>
        </tr>
        <tr>
            <th>Ghi chú đổi/trả</th>
            <td>{{ $order->return_note ?: 'Không có' }}</td>
        </tr>
        <tr>
            <th>Tổng tiền</th>
            <td class="total">{{ number_format($order->total_amount) }} ₫</td>
        </tr>
    </table>
</body>

</html>