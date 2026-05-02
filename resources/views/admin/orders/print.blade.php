<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vận đơn {{ $order->tracking_number ?: $order->order_code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 10px;
        }
        .waybill-container {
            border: 2px solid #000;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .row {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
        }
        .col-6 {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
            border-right: 2px solid #000;
        }
        .col-6:last-child {
            border-right: none;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-lg { font-size: 16px; }
        .text-xl { font-size: 20px; }
        .text-2xl { font-size: 24px; }
        
        .barcode-container {
            text-align: center;
            padding: 10px 0;
        }
        .barcode-img {
            max-width: 80%;
            height: 60px;
        }
        .tracking-text {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
            letter-spacing: 2px;
        }
        
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.items th, table.items td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        table.items th {
            background-color: #f0f0f0;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .footer-note {
            font-size: 11px;
            font-style: italic;
        }
        .cod-box {
            border: 2px solid #000;
            padding: 10px;
            text-align: center;
            margin-top: 10px;
            background-color: #f0f0f0;
        }
        .signature-box {
            height: 100px;
        }
    </style>
</head>
<body>

@php
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $barcodeText = $order->tracking_number ?: $order->order_code;
    $barcodeBase64 = base64_encode($generator->getBarcode($barcodeText, $generator::TYPE_CODE_128, 2, 60));
    
    $totalQty = $order->items->sum('quantity');
    $weight = max(1000, $totalQty * 200); // g
    $weightKg = $weight / 1000;
    
    $codAmount = in_array($order->payment_method, ['cod']) && $order->payment_status !== 'paid' ? $order->total_amount : 0;
    
    $carrier = $order->tracking_number ? 'GHN' : 'BEEPHONE DELIVERY';
@endphp

<div class="waybill-container">
    <!-- Header -->
    <div class="row">
        <div class="col-6">
            <div class="text-2xl font-bold">BEEPHONE</div>
            <div>Hệ thống bán lẻ điện thoại di động</div>
        </div>
        <div class="col-6 text-right">
            <div class="text-xl font-bold">{{ $carrier }}</div>
            <div class="font-bold">Mã vùng: {{ $order->ghn_ward_code ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Barcode -->
    <div class="row" style="border-bottom: 2px solid #000;">
        <div style="padding: 15px; text-align: center;">
            <div class="barcode-container">
                <img src="data:image/png;base64,{{ $barcodeBase64 }}" class="barcode-img">
                <div class="tracking-text">{{ $barcodeText }}</div>
            </div>
        </div>
    </div>

    <!-- Sender & Receiver -->
    <div class="row">
        <div class="col-6">
            <div class="section-title">Từ:</div>
            <div class="font-bold">BeePhone Store</div>
            <div>SĐT: 0987654321</div>
            <div>Địa chỉ: Tòa nhà FPT Polytechnic, Phố Trịnh Văn Bô, Nam Từ Liêm, Hà Nội.</div>
        </div>
        <div class="col-6">
            <div class="section-title">Đến:</div>
            <div class="font-bold">{{ $order->customer_name }}</div>
            <div>SĐT: {{ $order->customer_phone }}</div>
            <div>Địa chỉ: {{ $order->shipping_address ?: 'Chưa có địa chỉ' }}</div>
        </div>
    </div>

    <!-- Order Info & Note -->
    <div class="row">
        <div class="col-6">
            <div class="section-title">Nội dung hàng hóa:</div>
            <table class="items">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th style="width: 30px;" class="text-center">SL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 10px;">
                <strong>Tổng SL:</strong> {{ $totalQty }} | <strong>Khối lượng:</strong> {{ $weightKg }} kg
            </div>
        </div>
        <div class="col-6">
            <div class="cod-box">
                <div class="text-lg">Tiền thu người nhận:</div>
                <div class="text-2xl font-bold">{{ number_format($codAmount) }} VNĐ</div>
                @if($codAmount == 0)
                <div class="font-bold" style="color: #666;">(Đã thanh toán)</div>
                @endif
            </div>

            <div style="margin-top: 15px;">
                <strong>Chỉ dẫn giao hàng:</strong><br>
                1. Cho xem hàng, không thử.<br>
                2. Chuyển hoàn sau 3 lần phát không thành công.
            </div>
            
            @if($order->note)
            <div style="margin-top: 10px;">
                <strong>Ghi chú của khách:</strong><br>
                {{ $order->note }}
            </div>
            @endif
        </div>
    </div>

    <!-- Footer Signatures -->
    <div class="row" style="border-bottom: none;">
        <div class="col-6 text-center">
            <div class="font-bold">Chữ ký người gửi</div>
            <div class="footer-note">(Xác nhận nguyên vẹn)</div>
            <div class="signature-box"></div>
        </div>
        <div class="col-6 text-center">
            <div class="font-bold">Chữ ký người nhận</div>
            <div class="footer-note">(Xác nhận nhận đủ hàng)</div>
            <div class="signature-box"></div>
            <div>Ngày: ....../....../20...</div>
        </div>
    </div>
</div>

<div class="text-center" style="margin-top: 10px; font-size: 11px;">
    Mã đơn hàng: {{ $order->order_code }} - Ngày tạo: {{ $order->created_at->format('d/m/Y H:i') }} - Ngày in: {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>