<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    // 1. Hiện giao diện Thanh toán
    public function index()
    {
        $cart = Cart::with(['items.product', 'items.variant.attributeValues.attribute'])
            ->where(function($q) {
                if(Auth::check()) $q->where('user_id', Auth::id());
                else $q->where('session_id', Session::getId());
            })->first();

        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('client.products.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // Tính tổng tiền
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

    // 2. Xử lý lưu Đơn hàng vào DB
    public function process(Request $request)
    {
        // Bắt buộc nhập thông tin cơ bản
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
        ], [
            'customer_name.required' => 'Vui lòng nhập họ tên.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng.',
        ]);

        $cart = Cart::with(['items.product', 'items.variant.attributeValues.attribute'])
            ->where(function($q) {
                if(Auth::check()) $q->where('user_id', Auth::id());
                else $q->where('session_id', Session::getId());
            })->first();

        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('client.products.index')->with('error', 'Giỏ hàng trống!');
        }

        // Dùng DB Transaction: Nếu trừ kho lỗi thì hủy luôn việc tạo đơn
        DB::beginTransaction();
        try {
            $totalPrice = 0;

            // Tạo mã đơn hàng ngẫu nhiên (Ví dụ: ORD-1700001234)
            $orderCode = 'ORD-' . time();

            // 2.1 Tạo Đơn hàng (Bảng orders)
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
                'note' => $request->note,
                'ordered_at' => now(),
                'total_price' => 0, // Sẽ update sau khi loop items
                'total_amount' => 0,
            ]);

            // 2.2 Chuyển Item từ Cart sang Order & Trừ tồn kho
            foreach ($cart->items as $item) {
                $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
                $sku = $item->product->sku;
                $thumbnail = $item->product->thumbnail;
                $productName = $item->product->name;
                $stockObj = $item->product; // Để trừ kho

                if ($item->variant) {
                    $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                    $sku = $item->variant->sku;
                    $thumbnail = $item->variant->thumbnail ?? $item->product->thumbnail;
                    $variantName = $item->variant->attributeValues->pluck('value')->implode(' - ');
                    $productName = $item->product->name . ' (' . $variantName . ')';
                    $stockObj = $item->variant; // Trừ kho của biến thể
                }

                // Check kho lần cuối (đề phòng lúc khách đang đặt thì người khác mua mất)
                if ($stockObj->stock < $item->quantity) {
                    throw new \Exception('Sản phẩm ' . $productName . ' không đủ số lượng trong kho!');
                }

                $lineTotal = $price * $item->quantity;
                $totalPrice += $lineTotal;

                // Thêm vào bảng order_items
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

                // Trừ tồn kho
                $stockObj->decrement('stock', $item->quantity);
            }

            // 2.3 Cập nhật tổng tiền cho đơn hàng
            $order->update([
                'total_price' => $totalPrice,
                'total_amount' => $totalPrice // Nếu có mã giảm giá (voucher) thì trừ ở đây
            ]);

            // 2.4 Dọn dẹp Giỏ hàng
            $cart->items()->delete();
            $cart->delete();

            // Chốt giao dịch thành công
            DB::commit();

            // Chuyển hướng sang trang thành công
            return redirect()->route('client.checkout.success')->with('success', 'Bạn đã đặt hàng thành công! Mã đơn: ' . $orderCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    // 3. Trang thông báo Đặt hàng thành công
    public function success()
    {
        if (!session('success')) {
            return redirect()->route('home');
        }
        return view('client.checkout.success');
    }
}