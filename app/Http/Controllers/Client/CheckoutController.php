<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;

class CheckoutController extends Controller
{
    /**
     * 1. HÀM INDEX: Hiển thị giao diện trang Thanh toán
     */
    public function index()
    {
        $cart = Cart::with(['items.product', 'items.variant.attributeValues.attribute'])
            ->where(function($q) {
                if(Auth::check()) $q->where('user_id', Auth::id());
                else $q->where('session_id', Session::getId());
            })->first();

        // ---------------- THÊM ĐOẠN NÀY LÀ XONG ----------------
        // Lọc giỏ hàng: CHỈ giữ lại các item có ID nằm trong Session đã chọn
        $selectedIds = session('selected_cart_items', []);
        if (!empty($selectedIds)) {
            // Ép cái relationship items chỉ lấy những món khách chọn
            $cart->setRelation('items', $cart->items->whereIn('id', $selectedIds));
        }
        // --------------------------------------------------------

        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('client.products.index')->with('error', 'Không có sản phẩm nào để thanh toán!');
        }

        // Tính tổng tiền gốc (Chỉ tính các món đã chọn)
        $totalPrice = 0;
        foreach ($cart->items as $item) {
            $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
            if ($item->variant) {
                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
            }
            $totalPrice += $price * $item->quantity;
        }

        return view('client.checkout.index', compact('cart', 'totalPrice'));
    }

    /**
     * 2. HÀM PROCESS: Xử lý lưu Đơn hàng vào Database
     */
    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:cod,vnpay,wallet',
        ]);

        $cart = Cart::with(['items.product', 'items.variant.attributeValues.attribute'])
            ->where(function($q) {
                if(Auth::check()) $q->where('user_id', Auth::id());
                else $q->where('session_id', Session::getId());
            })->first();

        // Lọc giỏ hàng: CHỈ xử lý các item khách đã tích chọn
        $selectedIds = session('selected_cart_items', []);
        if (!empty($selectedIds)) {
            $cart->setRelation('items', $cart->items->whereIn('id', $selectedIds));
        }

        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('client.products.index')->with('error', 'Giỏ hàng trống hoặc chưa chọn sản phẩm!');
        }

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $orderCode = 'ORD-' . time();

            // 2.1 Tạo Đơn hàng (Khởi tạo)
            $order = Order::create([
                'user_id' => Auth::id() ?? null,
                'order_code' => $orderCode,
                'customer_name' => $request->customer_name,
                'phone' => $request->customer_phone, 
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email ?? (Auth::check() ? Auth::user()->email : null),
                'recipient_name' => $request->customer_name,
                'recipient_phone' => $request->customer_phone,
                'recipient_address' => $request->shipping_address,
                'shipping_address' => $request->shipping_address,
                'address' => $request->shipping_address,
                'status' => 'pending',
                'return_status' => 'none',
                'payment_method' => $request->payment_method, 
                'note' => $request->note,
                'ordered_at' => now(),
                'total_price' => 0, 
                'total_amount' => 0,
            ]);

            // 2.2 Chuyển Item từ Cart sang Order & Trừ tồn kho
            foreach ($cart->items as $item) {
                $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
                $sku = $item->product->sku;
                $thumbnail = $item->product->thumbnail;
                $productName = $item->product->name;
                $stockObj = $item->product;

                if ($item->variant) {
                    $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                    $sku = $item->variant->sku;
                    $thumbnail = $item->variant->thumbnail ?? $item->product->thumbnail;
                    $variantName = $item->variant->attributeValues->pluck('value')->implode(' - ');
                    $productName = $item->product->name . ' (' . $variantName . ')';
                    $stockObj = $item->variant; 
                }

                if ($stockObj->stock < $item->quantity) {
                    throw new \Exception('Sản phẩm ' . $productName . ' không đủ số lượng trong kho!');
                }

                $lineTotal = $price * $item->quantity;
                $totalPrice += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $productName,
                    'product_sku' => $sku,
                    'thumbnail' => $thumbnail,
                    'unit_price' => $price,
                    'quantity' => $item->quantity,
                    'line_total' => $lineTotal,
                ]);

                $stockObj->decrement('stock', $item->quantity);
            }

            // 2.3 XỬ LÝ VOUCHER (Upsert chống lỗi Duplicate 1062)
            $discountAmount = 0;
            if (session()->has('voucher')) {
                $voucherSession = session('voucher');
                $discountAmount = $voucherSession['discount_amount'];

                $voucher = Voucher::find($voucherSession['id']);
                if ($voucher) {
                    $voucher->increment('used_count');

                    if (Auth::check()) {
                        $user = Auth::user();
                        $user->userVouchers()->syncWithoutDetaching([
                            $voucher->id => [
                                'order_id' => $order->id,
                                'used_at' => now()
                            ]
                        ]);
                    }
                }
            }

            $finalAmount = $totalPrice - $discountAmount;
            if ($finalAmount < 0) $finalAmount = 0;

            $order->update([
                'total_price' => $totalPrice, 
                'total_amount' => $finalAmount 
            ]);

            // 2.4 LOGIC THANH TOÁN BẰNG VÍ BEE PAY
            if ($request->payment_method === 'wallet') {
                $user = Auth::user();
                if (!$user) throw new \Exception('Vui lòng đăng nhập để thanh toán bằng Ví Bee Pay!');

                $wallet = Wallet::where('user_id', $user->id)->first();
                if (!$wallet || $wallet->balance < $finalAmount) {
                    throw new \Exception('Số dư Ví Bee Pay không đủ. Vui lòng nạp thêm tiền!');
                }

                $balanceBefore = $wallet->balance;
                $wallet->decrement('balance', $finalAmount);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'payment',
                    'amount' => $finalAmount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance, 
                    'description' => 'Thanh toán đơn hàng ' . $orderCode,
                    'reference_type' => 'App\Models\Order',
                    'reference_id' => $order->id,
                    'status' => 'completed',
                ]);

                $order->update(['payment_status' => 'paid']);
            }

            // ==========================================
            // 2.5 DỌN DẸP GIỎ HÀNG (CHỈ XÓA MÓN ĐÃ MUA)
            // ==========================================
            if (!empty($selectedIds)) {
                // Chỉ xóa các item được tích chọn thanh toán
                CartItem::whereIn('id', $selectedIds)->delete();
            } else {
                $cart->items()->delete(); // Mặc định thì xóa hết (nếu có lỗi gì đó)
            }

            // Kiểm tra xem giỏ hàng còn món nào không, nếu không còn thì xóa luôn cái vỏ Cart
            if ($cart->items()->count() == 0) {
                $cart->delete();
            }

            // Xóa session voucher và session chọn món để khách mua đơn tiếp theo
            session()->forget('voucher'); 
            session()->forget('selected_cart_items');

            DB::commit();

            // ==========================================
            // LƯU SESSION ID ĐỂ HIỂN THỊ GIAO DIỆN SUCCESS
            // ==========================================
            session(['new_order_id' => $order->id]);

            // Gửi email
            $customerEmail = $order->customer_email;
            if (!empty($customerEmail)) {
                try {
                    Mail::to($customerEmail)->send(new OrderPlacedMail($order));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Lỗi gửi email xác nhận đơn: ' . $e->getMessage());
                }
            }

            // 2.6 LOGIC VNPAY
            if ($request->payment_method === 'vnpay') {
                if ($finalAmount <= 0) {
                    $order->update(['payment_status' => 'paid']);
                    return redirect()->route('client.checkout.success')
                        ->with('success', 'Đơn hàng thành công!')
                        ->with('last_order_id', $order->id);
                }

                $vnp_Url = env('VNPAY_URL');
                $vnp_Returnurl = route('vnpay.return');
                $vnp_TmnCode = env('VNPAY_TMN_CODE');
                $vnp_HashSecret = env('VNPAY_HASH_SECRET');

                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $finalAmount * 100,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
                    "vnp_Locale" => 'vn',
                    "vnp_OrderInfo" => "Thanh toan don hang " . $orderCode,
                    "vnp_OrderType" => 'billpayment',
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $order->id,
                );

                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }

                return redirect($vnp_Url);
            }

            return redirect()->route('client.checkout.success')
                ->with('success', 'Bạn đã đặt hàng thành công! Mã đơn: ' . $orderCode)
                ->with('last_order_id', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * 3. HÀM XỬ LÝ VNPAY_RETURN
     */
    public function vnpay_return(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $order = Order::find($request->vnp_TxnRef);

        if ($secureHash == $vnp_SecureHash) {
            // LƯU SESSION ĐỂ HIỆN TRANG SUCCESS (NẾU KHÁCH TỪ VNPAY QUAY VỀ)
            if ($order) {
                session(['new_order_id' => $order->id]);
            }

            if ($request->vnp_ResponseCode == '00') {
                if ($order) {
                    $order->update(['payment_status' => 'paid']); 
                }
                return redirect()->route('client.checkout.success')
                    ->with('success', 'Thanh toán VNPAY thành công!')
                    ->with('last_order_id', $order?->id);
            } else {
                if ($order) {
                    $order->update(['status' => 'cancelled']);
                }
                return redirect()->route('client.checkout.index')->with('error', 'Thanh toán thất bại!');
            }
        } else {
            return redirect()->route('client.checkout.index')->with('error', 'Lỗi bảo mật VNPAY!');
        }
    }

    /**
     * 4. TRANG SUCCESS
     */
    public function success()
    {
// <<<<<<< vinh1
//         if (!session('success') && !session('last_order_id')) {
//             return redirect()->route('home');
//         }

//         $order = null;
//         $orderId = session('last_order_id');
//         if ($orderId) {
//             $order = Order::query()
//                 ->with(['items.product'])
//                 ->find($orderId);

//             if ($order && $order->user_id && Auth::check() && $order->user_id !== Auth::id()) {
//                 $order = null;
//             }
//         }

//         return view('client.checkout.success', compact('order'));
// =======
        // Phải có 1 trong 2 thông báo success hoặc có ID đơn hàng mới cho xem
        if (!session('success') || !session('new_order_id')) {
            return redirect()->route('home');
        }

        // Truy vấn lấy đơn hàng và danh sách món hàng
        $order = Order::with('items')->find(session('new_order_id'));

        // Nếu vì lý do tâm linh nào đó mất đơn hàng thì đuổi về home
        if (!$order) {
            return redirect()->route('home');
        }

        return view('client.checkout.success', compact('order'));
    }

    /**
     * 5. HỦY MÃ GIẢM GIÁ TẠI CHECKOUT
     */
    public function removeVoucher()
    {
        session()->forget('voucher');
        return response()->json([
            'success' => true, 
            'message' => 'Đã hủy mã giảm giá!'
        ]);
// >>>>>>> main
    }
}
