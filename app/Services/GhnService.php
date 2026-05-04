<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GhnService
{
    protected string $apiUrl;
    protected string $token;
    protected int $shopId;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.ghn.api_url', env('GHN_API_URL', 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/')), '/');
        $this->token  = config('services.ghn.token', env('GHN_TOKEN', ''));
        $this->shopId = (int) config('services.ghn.shop_id', env('GHN_SHOP_ID', 0));
    }

    /**
     * Lấy thông tin kho gửi từ GHN API (POST /v2/shop/all).
     * Kết quả được cache 1 giờ để tránh gọi API liên tục.
     * Fallback về biến môi trường nếu API không phản hồi.
     *
     * @return array{name: string, phone: string, address: string, district_id: int, ward_code: string}
     */
    public function getShopInfo(): array
    {
        $cacheKey = "ghn_shop_info_{$this->shopId}";

        return Cache::remember($cacheKey, now()->addHour(), function () {
            try {
                $response = Http::withoutVerifying()->withHeaders([
                    'Token'        => $this->token,
                    'Content-Type' => 'application/json',
                ])->post($this->apiUrl . '/shop/all', [
                    'offset' => 0,
                    'limit'  => 50,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['code'] ?? null) === 200) {
                        $shops = $data['data']['shops'] ?? [];
                        // Tìm đúng shop theo shopId
                        $shop = collect($shops)->firstWhere('_id', $this->shopId)
                              ?? collect($shops)->first();

                        if ($shop) {
                            Log::info('[GHN] getShopInfo: Lấy thông tin kho thành công từ API', [
                                'shop_id'     => $this->shopId,
                                'name'        => $shop['name'] ?? '',
                                'district_id' => $shop['district_id'] ?? 0,
                                'ward_code'   => $shop['ward_code'] ?? '',
                            ]);

                            return [
                                'name'        => $shop['name']        ?? env('GHN_FROM_NAME', 'BeePhone Store'),
                                'phone'       => $shop['phone']       ?? env('GHN_FROM_PHONE', ''),
                                'address'     => $shop['address']     ?? env('GHN_FROM_ADDRESS', ''),
                                'district_id' => (int) ($shop['district_id'] ?? env('GHN_FROM_DISTRICT_ID', 0)),
                                'ward_code'   => (string) ($shop['ward_code'] ?? env('GHN_FROM_WARD_CODE', '')),
                            ];
                        }
                    }
                    Log::warning('[GHN] getShopInfo: API trả lỗi hoặc không tìm thấy shop', [
                        'shop_id'  => $this->shopId,
                        'response' => $data,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('[GHN] getShopInfo Exception', ['error' => $e->getMessage()]);
            }

            // Fallback về env
            Log::warning('[GHN] getShopInfo: Dùng fallback từ .env');
            return [
                'name'        => env('GHN_FROM_NAME', 'BeePhone Store'),
                'phone'       => env('GHN_FROM_PHONE', ''),
                'address'     => env('GHN_FROM_ADDRESS', ''),
                'district_id' => (int) env('GHN_FROM_DISTRICT_ID', 0),
                'ward_code'   => (string) env('GHN_FROM_WARD_CODE', ''),
            ];
        });
    }

    /**
     * Lấy thông tin chi tiết một đơn hàng GHN theo mã vận đơn (order_code).
     * Response bao gồm field `log[]` với các bước trạng thái.
     *
     * @param  string $orderCode Mã vận đơn GHN (VD: GHN12345678)
     * @return array|null        Mảng dữ liệu đơn hàng hoặc null nếu lỗi
     */
    public function getOrderDetail(string $orderCode): ?array
    {
        try {
            $endpoint = $this->apiUrl . '/shipping-order/detail';

            $response = Http::withoutVerifying()->withHeaders([
                'Token'        => $this->token,
                'ShopId'       => $this->shopId,
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'order_code' => $orderCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] === 200) {
                    return $data['data'] ?? null;
                }
                Log::warning('[GHN] getOrderDetail: API trả lỗi', [
                    'order_code' => $orderCode,
                    'response'   => $data,
                ]);
            } else {
                Log::error('[GHN] getOrderDetail: HTTP lỗi', [
                    'order_code' => $orderCode,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[GHN] getOrderDetail: Exception', [
                'order_code' => $orderCode,
                'error'      => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Lấy lịch sử tracking đầy đủ của một đơn hàng GHN.
     * Endpoint: POST /v2/shipping-order/tracking
     * Đây là API chuyên dụng cho tracking, trả về toàn bộ sự kiện vận chuyển
     * bao gồm cả các bước chi tiết hơn log[] trong getOrderDetail().
     *
     * Response mẫu mỗi entry:
     * {
     *   "status": "picking",
     *   "description": "Nhân viên đang lấy hàng tại...",
     *   "updated_date": 1714377600   (Unix timestamp seconds hoặc milliseconds)
     * }
     *
     * @param  string $orderCode Mã vận đơn GHN
     * @return array|null        Mảng tracking events hoặc null nếu lỗi/không hỗ trợ
     */
    public function getOrderTracking(string $orderCode): ?array
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Token'        => $this->token,
                'ShopId'       => $this->shopId,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/shipping-order/tracking', [
                'order_code' => $orderCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] === 200) {
                    $result = $data['data'] ?? null;
                    // GHN trả về mảng trực tiếp hoặc lồng trong data
                    if (is_array($result) && !empty($result)) {
                        return $result;
                    }
                }
                Log::info('[GHN] getOrderTracking: Không có dữ liệu hoặc API không hỗ trợ', [
                    'order_code' => $orderCode,
                    'response'   => $data,
                ]);
            } else {
                Log::warning('[GHN] getOrderTracking: HTTP lỗi', [
                    'order_code' => $orderCode,
                    'status'     => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[GHN] getOrderTracking: Exception', [
                'order_code' => $orderCode,
                'error'      => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Lấy danh sách đơn hàng của shop từ GHN (theo trang).
     * GHN API endpoint: /v2/shipping-order/get-list-by-shop
     *
     * @param  int $page     Số trang (bắt đầu từ 1)
     * @param  int $limit    Số đơn mỗi trang (tối đa 100)
     * @param  string|null $fromDate  Lọc từ ngày (ISO 8601, VD: 2024-01-01T00:00:00Z)
     * @param  string|null $toDate    Lọc đến ngày
     * @return array|null
     */
    public function getOrderList(int $page = 1, int $limit = 100, ?string $fromDate = null, ?string $toDate = null): ?array
    {
        try {
            $payload = [
                'shop_id'   => $this->shopId,
                'offset'    => ($page - 1) * $limit,
                'limit'     => $limit,
            ];

            if ($fromDate) {
                $payload['from_time'] = $fromDate;
            }
            if ($toDate) {
                $payload['to_time'] = $toDate;
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Token'        => $this->token,
                'ShopId'       => $this->shopId,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/shipping-order/get-list', $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] === 200) {
                    return $data['data'] ?? null;
                }
                Log::warning('[GHN] getOrderList: API trả lỗi', ['response' => $data]);
            } else {
                Log::error('[GHN] getOrderList: HTTP lỗi', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[GHN] getOrderList: Exception', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Tạo đơn hàng vận chuyển trên GHN.
     * Được gọi khi Admin duyệt đơn (pending → packing).
     *
     * @param  \App\Models\Order $order  Đơn hàng cần tạo vận đơn
     * @return string|null               Mã vận đơn GHN (order_code) hoặc null nếu lỗi
     */
    public function createOrder(\App\Models\Order $order): ?string
    {
        // Kiểm tra bắt buộc: cần mã district và ward của GHN
        if (!$order->ghn_district_id || !$order->ghn_ward_code) {
            Log::warning('[GHN] createOrder: Thiếu mã vùng GHN (district/ward). Đơn hàng được tạo trước khi tích hợp GHN API.', [
                'order_code'      => $order->order_code,
                'ghn_district_id' => $order->ghn_district_id,
                'ghn_ward_code'   => $order->ghn_ward_code,
            ]);
            return null;
        }

        try {
            // Tổng COD: nếu COD thì thu tiền, còn lại đã thanh toán trước thì = 0
            $codAmount = in_array($order->payment_method, ['cod'])
                ? (int) $order->total_amount
                : 0;

            // Tính tổng khối lượng ước tính (mỗi sản phẩm ~200g)
            $totalQty = $order->items->sum('quantity');
            $weight   = max(1000, $totalQty * 200); // tối thiểu 1kg

            // Danh sách sản phẩm cho GHN
            $items = $order->items->map(fn ($i) => [
                'name'     => mb_substr($i->product_name, 0, 100),
                'quantity' => (int) $i->quantity,
                'price'    => (int) $i->unit_price,
                'weight'   => 200,
            ])->values()->toArray();

            // Lấy thông tin kho gửi từ GHN API (có cache)
            $shopInfo = $this->getShopInfo();

            $payload = [
                'payment_type_id'  => 1,           // 1 = người bán trả phí ship
                'note'             => $order->note ?? 'Không có ghi chú',
                'required_note'    => 'CHOXEMHANGKHONGTHU',
                'client_order_code'=> $order->order_code, // Mã đơn hàng nội bộ để map với GHN

                // Địa chỉ kho gửi (lấy từ GHN API)
                'from_name'        => $shopInfo['name'],
                'from_phone'       => $shopInfo['phone'],
                'from_address'     => $shopInfo['address'],
                'from_district_id' => $shopInfo['district_id'],
                'from_ward_code'   => $shopInfo['ward_code'],

                // Địa chỉ nhận
                'to_name'          => $order->customer_name,
                'to_phone'         => $order->customer_phone,
                'to_address'       => $order->shipping_address,
                'to_district_id'   => (int) $order->ghn_district_id,
                'to_ward_code'     => (string) $order->ghn_ward_code,

                'cod_amount'       => $codAmount,
                'weight'           => $weight,
                'length'           => 20,
                'width'            => 15,
                'height'           => 10,
                'service_type_id'  => 2,
                'items'            => $items,
            ];

            $response = Http::withoutVerifying()->withHeaders([
                'Token'        => $this->token,
                'ShopId'       => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/shipping-order/create', $payload);

            $data = $response->json();

            Log::info('[GHN] createOrder response', [
                'order_code' => $order->order_code,
                'response'   => $data,
            ]);

            if (($data['code'] ?? null) === 200) {
                return $data['data']['order_code'] ?? null;
            }

            Log::warning('[GHN] createOrder thất bại', [
                'order_code' => $order->order_code,
                'response'   => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('[GHN] createOrder Exception', [
                'order_code' => $order->order_code,
                'error'      => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Tạo đơn hàng thu hồi (Return) trên GHN.
     * Được gọi khi Admin duyệt yêu cầu trả hàng.
     *
     * @param  \App\Models\ReturnRequest $returnRequest
     * @return string|null               Mã vận đơn GHN (order_code) hoặc null nếu lỗi
     */
    public function createReturnOrder(\App\Models\ReturnRequest $returnRequest): ?string
    {
        $order = $returnRequest->order;
        if (!$order || !$order->ghn_district_id || !$order->ghn_ward_code) {
            Log::warning('[GHN] createReturnOrder: Thiếu thông tin địa chỉ KH.', [
                'order_code' => $order?->order_code,
            ]);
            return null;
        }

        try {
            $items = [];
            $totalWeight = 0;
            foreach ($returnRequest->items as $reqItem) {
                $orderItem = $reqItem->orderItem;
                $items[] = [
                    'name'     => mb_substr($orderItem->product_name, 0, 100),
                    'quantity' => (int) $reqItem->quantity,
                    'price'    => (int) $orderItem->unit_price,
                    'weight'   => 200,
                ];
                $totalWeight += ($reqItem->quantity * 200);
            }

            $payload = [
                'payment_type_id'  => 1, // 1 = Shop trả phí ship thu hồi
                'note'             => "Thu hồi hàng trả cho đơn gốc #{$order->order_code}",
                'required_note'    => 'CHOXEMHANGKHONGTHU',
                'client_order_code'=> "RET_{$returnRequest->id}_{$order->order_code}",
                
                // Từ khách hàng
                'from_name'        => $order->customer_name,
                'from_phone'       => $order->customer_phone,
                'from_address'     => $order->shipping_address,
                'from_district_id' => (int) $order->ghn_district_id,
                'from_ward_code'   => (string) $order->ghn_ward_code,

                // Đến shop
                'to_name'          => 'Kho BeePhone (Thu hồi)',
                'to_phone'         => '0987654321',
                'to_address'       => 'Hà Nội',
                'to_district_id'   => 3440, // Quận Nam Từ Liêm
                'to_ward_code'     => '13010', // Phường Phương Canh
                
                'cod_amount'       => 0,
                'weight'           => max(200, $totalWeight),
                'length'           => 15,
                'width'            => 15,
                'height'           => 10,
                'service_type_id'  => 2,
                'items'            => $items,
            ];

            $response = Http::withoutVerifying()->withHeaders([
                'Token'        => $this->token,
                'ShopId'       => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/shipping-order/create', $payload);

            $data = $response->json();

            Log::info('[GHN] createReturnOrder response', [
                'order_code' => $order->order_code,
                'response'   => $data,
            ]);

            if (($data['code'] ?? null) === 200) {
                return $data['data']['order_code'] ?? null;
            }

            Log::warning('[GHN] createReturnOrder thất bại', [
                'order_code' => $order->order_code,
                'response'   => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('[GHN] createReturnOrder Exception', [
                'order_code' => $order->order_code ?? '',
                'error'      => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Tính phí vận chuyển qua GHN.
     *
     * @param int $fromDistrictId Từ quận/huyện
     * @param string $fromWardCode Từ phường/xã
     * @param int $toDistrictId Đến quận/huyện
     * @param string $toWardCode Đến phường/xã
     * @param int $weight Khối lượng (gram)
     * @return int Phí vận chuyển (hoặc 30000 nếu lỗi)
     */
    public function calculateFee(int $fromDistrictId, string $fromWardCode, int $toDistrictId, string $toWardCode, int $weight): int
    {
        try {
            $payload = [
                'service_type_id' => 2,
                'from_district_id' => $fromDistrictId,
                'from_ward_code' => $fromWardCode,
                'to_district_id' => $toDistrictId,
                'to_ward_code' => $toWardCode,
                'weight' => max(200, $weight),
                'length' => 15,
                'width' => 15,
                'height' => 10,
            ];

            $response = Http::withoutVerifying()->withHeaders([
                'Token' => $this->token,
                'ShopId' => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/shipping-order/fee', $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] === 200) {
                    return (int) ($data['data']['total'] ?? 30000);
                }
            }
        } catch (\Throwable $e) {
            Log::error('[GHN] calculateFee Exception', ['error' => $e->getMessage()]);
        }
        return 30000; // fallback fee
    }
    /**
     * Hủy đơn hàng trên GHN
     *
     * @param string|array $trackingNumbers Mã vận đơn GHN
     * @return bool Trạng thái hủy thành công
     */
    public function cancelOrder($trackingNumbers): bool
    {
        if (empty($trackingNumbers)) {
            return false;
        }

        if (!is_array($trackingNumbers)) {
            $trackingNumbers = [$trackingNumbers];
        }

        try {
            $payload = [
                'order_codes' => $trackingNumbers
            ];

            $response = Http::withoutVerifying()->withHeaders([
                'Token'        => $this->token,
                'ShopId'       => (string) $this->shopId,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/switch-status/cancel', $payload);

            $data = $response->json();

            Log::info('[GHN] cancelOrder response', [
                'order_codes' => $trackingNumbers,
                'response'    => $data,
            ]);

            return ($data['code'] ?? null) === 200;
        } catch (\Throwable $e) {
            Log::error('[GHN] cancelOrder Exception', [
                'order_codes' => $trackingNumbers,
                'error'       => $e->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * Tạo token để in đơn hàng trên GHN
     *
     * @param array $orderCodes Danh sách mã vận đơn GHN
     * @return string|null
     */
    public function generatePrintToken(array $orderCodes): ?string
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Token'        => $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/a5/gen-token', [
                'order_codes' => $orderCodes,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] === 200) {
                    return $data['data']['token'] ?? null;
                }
                Log::warning('[GHN] generatePrintToken: API trả lỗi', ['response' => $data]);
            } else {
                Log::error('[GHN] generatePrintToken: HTTP lỗi', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[GHN] generatePrintToken Exception', ['error' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * Lấy đường dẫn in đơn hàng
     *
     * @param string $token Token in đơn
     * @param string $size Kích thước in (A5, 80x80, 52x70)
     * @return string
     */
    public function getPrintUrl(string $token, string $size = 'A5'): string
    {
        $baseUrl = str_replace('/shiip/public-api/v2', '', $this->apiUrl);
        return "{$baseUrl}/a5/public-api/print{$size}?token={$token}";
    }

    /**
     * Map trạng thái GHN sang trạng thái nội bộ của hệ thống BeePhone.
     *
     * @param  string $ghnStatus Trạng thái từ GHN
     * @return string|null       Trạng thái nội bộ hoặc null nếu không cần cập nhật
     */
    public function mapStatus(string $ghnStatus): ?string
    {
        $s = strtolower(trim($ghnStatus));

        $known = [
            'ready_to_pick', 'picking', 'money_collect_picking',
            'picked', 'storing', 'transporting', 'sorting',
            'delivering', 'money_collect_delivering',
            'delivered', 'delivery_fail',
            'waiting_to_return', 'return', 'return_transporting',
            'return_sorting', 'returning', 'return_fail', 'returned',
            'exception', 'damage', 'lost',
            'cancel', 'cancelled',
        ];

        if (in_array($s, $known, true)) {
            // 'cancelled' từ GHN → normalize về 'cancel'
            return $s === 'cancelled' ? 'cancel' : $s;
        }

        return null; // status không xác định → bỏ qua
    }
}
