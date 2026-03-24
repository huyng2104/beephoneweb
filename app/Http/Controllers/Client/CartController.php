<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    // Hàm dùng chung để tìm hoặc tạo Giỏ hàng
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            // Đảm bảo session_id luôn tồn tại
            Session::start(); 
            return Cart::firstOrCreate(['session_id' => Session::getId()]);
        }
    }

    public function add(Request $request)
    {
        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $quantity = (int) $request->quantity;

        // 1. Kiểm tra Sản phẩm & Biến thể
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại!'], 404);
        }

        if ($product->type == 'variable' && empty($variantId)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn phiên bản sản phẩm!'], 400);
        }

        // Lấy số lượng tồn kho
        $stock = $product->stock;
        if ($product->type == 'variable') {
            $variant = ProductVariant::find($variantId);
            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Phiên bản không tồn tại!'], 404);
            }
            $stock = $variant->stock;
        }

        if ($stock < $quantity) {
            return response()->json(['success' => false, 'message' => 'Kho không đủ số lượng!'], 400);
        }

        // 2. Lấy Giỏ hàng hiện tại (Từ DB)
        $cart = $this->getCart();

        // 3. Thêm hoặc Cập nhật món hàng
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($cartItem) {
            // Nếu có rồi, check xem cộng thêm có lố kho không
            if ($cartItem->quantity + $quantity > $stock) {
                return response()->json(['success' => false, 'message' => 'Vượt quá số lượng tồn kho!'], 400);
            }
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Chưa có thì tạo mới
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        // 4. Tính tổng số món đang có trong giỏ
        $totalItems = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json([
            'success' => true, 
            'message' => 'Đã thêm vào giỏ hàng!',
            'cart_count' => $totalItems
        ]);
    }

    public function count()
    {
        $cart = $this->getCart();
        $totalItems = CartItem::where('cart_id', $cart->id)->sum('quantity');
        return response()->json(['count' => $totalItems]);
    }

    // Hiển thị trang Giỏ hàng
    public function index()
    {
        $cart = $this->getCart();
        // Lấy tất cả sản phẩm trong giỏ, kèm theo thông tin Product và Variant
        $cartItems = CartItem::with(['product', 'variant.attributeValues.attribute'])
            ->where('cart_id', $cart->id)
            ->latest()
            ->get();

        // Tính tổng tiền (Giá mượt mà, ưu tiên giá sale)
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
            if ($item->variant) {
                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
            }
            $totalPrice += $price * $item->quantity;
        }

        return view('client.cart.index', compact('cartItems', 'totalPrice'));
    }

    // Cập nhật số lượng
    public function update(Request $request)
    {
        $cartItem = CartItem::find($request->item_id);
        if ($cartItem) {
            $cartItem->quantity = $request->quantity;
            $cartItem->save();
            
            // ĐÃ THÊM: Xóa bộ nhớ Voucher khi thay đổi số lượng
            session()->forget('voucher');

            return response()->json(['success' => true, 'message' => 'Đã cập nhật số lượng!']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    }

    // Xóa khỏi giỏ
    public function remove(Request $request)
    {
        $cartItem = CartItem::find($request->item_id);
        if ($cartItem) {
            $cartItem->delete();

            // ĐÃ THÊM: Xóa bộ nhớ Voucher khi xóa sản phẩm
            session()->forget('voucher');

            return response()->json(['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng!']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
    }

    // Hàm xử lý Áp dụng mã giảm giá
    public function applyVoucher(Request $request)
    {
        $code = $request->code;
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá!']);
        }

        // Tìm mã giảm giá trong DB
        $voucher = \App\Models\Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại!']);
        }

        // --- CHECK ĐIỀU KIỆN THEO ĐÚNG DB CỦA BRO ---
        if ($voucher->status != 1) return response()->json(['success' => false, 'message' => 'Mã giảm giá đã ngưng hoạt động!']);
        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!']);
        if ($voucher->start_date && now() < $voucher->start_date) return response()->json(['success' => false, 'message' => 'Mã chưa đến thời gian sử dụng!']);
        if ($voucher->end_date && now() > $voucher->end_date) return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn!']);

        // Tính tổng tiền giỏ hàng hiện tại
        $cart = \App\Models\Cart::with(['items.product', 'items.variant'])
            ->where(function($q) {
                if(\Illuminate\Support\Facades\Auth::check()) $q->where('user_id', \Illuminate\Support\Facades\Auth::id());
                else $q->where('session_id', \Illuminate\Support\Facades\Session::getId());
            })->first();

        $totalPrice = 0;
        if ($cart) {
            foreach ($cart->items as $item) {
                $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
                if ($item->variant) {
                    $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                }
                $totalPrice += $price * $item->quantity;
            }
        }

        // Check đơn hàng tối thiểu
        if ($voucher->min_order_value && $totalPrice < $voucher->min_order_value) {
            return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . 'đ!']);
        }

        // Tính toán số tiền được giảm
        $discountAmount = 0;
        if ($voucher->discount_type === 'percent') { // Giảm theo %
            $discountAmount = ($totalPrice * $voucher->discount_value) / 100;
            // Check giảm tối đa
            if ($voucher->max_discount && $discountAmount > $voucher->max_discount) {
                $discountAmount = $voucher->max_discount;
            }
        } else { // Giảm số tiền cố định (fixed)
            $discountAmount = $voucher->discount_value; 
        }

        // Không cho phép giảm lố tiền đơn hàng
        if ($discountAmount > $totalPrice) {
            $discountAmount = $totalPrice;
        }

        // Lưu voucher vào Session để qua trang Checkout trừ tiền
        session(['voucher' => [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'discount_amount' => $discountAmount
        ]]);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã thành công!',
            'discount_formatted' => number_format($discountAmount, 0, ',', '.'),
            'new_total' => number_format($totalPrice - $discountAmount, 0, ',', '.')
        ]);
    }

    // Xử lý nút Thanh toán ở Giỏ hàng (Chỉ lưu các món đã tích chọn)
    public function checkoutSelect(Request $request)
    {
        if (!$request->has('selected_items') || count($request->selected_items) == 0) {
            return back()->with('error', 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
        }

        // Lưu mảng ID các item được tích vào Session
        session(['selected_cart_items' => $request->selected_items]);
        
        return redirect()->route('client.checkout.index');
    }
}