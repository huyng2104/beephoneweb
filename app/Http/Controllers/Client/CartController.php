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

        // Nếu là sản phẩm đơn mà truyền variantId trống thì tự lấy variant đầu tiên
        if ($product->type == 'simple' && empty($variantId)) {
            $variantId = $product->variants()->first()->id ?? null;
        }

        if (empty($variantId)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn phiên bản sản phẩm!'], 400);
        }

        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Phiên bản không tồn tại!'], 404);
        }
        $stock = $variant->stock;

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
            $price = 0;
            if ($item->variant) {
                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
            } else {
                $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
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
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn mã giảm giá trong ví!']);
        }

        // 1. Phải đăng nhập mới dùng được voucher
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để sử dụng mã giảm giá!']);
        }
        $user = Auth::user();

        // 2. Tìm mã giảm giá trong DB
        $voucher = \App\Models\Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại!']);
        }

        // 3. Kiểm tra xem voucher có trong ví của người dùng và chưa sử dụng không
        // Giả sử userVouchers() trả về mối quan hệ belongsToMany với bảng user_vouchers
        // Chúng ta cần những row chưa có order_id (chưa dùng)
        $isOwned = \DB::table('user_vouchers')
            ->where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->whereNull('order_id')
            ->exists();
        
        if (!$isOwned) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá này chưa có trong ví của bạn hoặc đã được sử dụng!']);
        }

        // 4. Kiểm tra các điều kiện cơ bản
        if ($voucher->status != 1) return response()->json(['success' => false, 'message' => 'Mã giảm giá đã ngưng hoạt động!']);
        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!']);
        if ($voucher->start_date && now() < $voucher->start_date) return response()->json(['success' => false, 'message' => 'Mã chưa đến thời gian sử dụng!']);
        if ($voucher->end_date && now() > $voucher->end_date) return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn!']);

        // 5. Lấy giỏ hàng và lọc món ăn đã chọn
        $cart = \App\Models\Cart::with(['items.product.categories', 'items.variant'])
            ->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->count() == 0) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống!']);
        }

        $selectedIds = session('selected_cart_items', []);
        if (!empty($selectedIds)) {
            $cart->setRelation('items', $cart->items->whereIn('id', $selectedIds));
        }

        if ($cart->items->count() == 0) {
            return response()->json(['success' => false, 'message' => 'Bạn chưa chọn sản phẩm nào để thanh toán!']);
        }

        // 6. KIỂM TRA RÀNG BUỘC SẢN PHẨM / DANH MỤC / THƯƠNG HIỆU
        // Lấy danh sách ID được phép
        $allowedProductIds = $voucher->products()->pluck('products.id')->toArray();
        $allowedCategoryIds = $voucher->categories()->pluck('categories.id')->toArray();
        $allowedBrandIds = $voucher->brands()->pluck('brands.id')->toArray();

        $hasRestrictions = !empty($allowedProductIds) || !empty($allowedCategoryIds) || !empty($allowedBrandIds);

        $totalPrice = 0;
        $eligibleTotalPrice = 0;

        foreach ($cart->items as $item) {
            $price = 0;
            if ($item->variant) {
                $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
            } else {
                $price = $item->product->sale_price > 0 ? $item->product->sale_price : $item->product->price;
            }
            $itemTotal = $price * $item->quantity;
            $totalPrice += $itemTotal;

            if ($hasRestrictions) {
                $isEligible = true; // Mặc định cho pass, sau đó loại trừ dần

                // 1. Phải khớp Sản phẩm (nếu có cấu hình)
                if (!empty($allowedProductIds) && !in_array($item->product_id, $allowedProductIds)) {
                    $isEligible = false;
                }
                
                // 2. Phải khớp Thương hiệu (nếu có cấu hình)
                if ($isEligible && !empty($allowedBrandIds) && !in_array($item->product->brand_id, $allowedBrandIds)) {
                    $isEligible = false;
                }
                
                // 3. Phải khớp Danh mục (nếu có cấu hình)
                if ($isEligible && !empty($allowedCategoryIds)) {
                    $itemCategoryIds = $item->product->categories->pluck('id')->toArray();
                    if (empty(array_intersect($itemCategoryIds, $allowedCategoryIds))) {
                        $isEligible = false;
                    }
                }
                
                if ($isEligible) {
                    $eligibleTotalPrice += $itemTotal;
                } else {
                    // Nếu có BẤT KỲ sản phẩm nào không thỏa mãn điều kiện, từ chối áp dụng voucher luôn
                    return response()->json([
                        'success' => false, 
                        'message' => 'Sản phẩm "' . $item->product->name . '" không được áp dụng mã giảm giá này!'
                    ]);
                }
            } else {
                $eligibleTotalPrice += $itemTotal;
            }
        }

        // 7. Check đơn hàng tối thiểu đối với các sản phẩm hợp lệ
        if ($voucher->min_order_value && $eligibleTotalPrice < $voucher->min_order_value) {
            return response()->json(['success' => false, 'message' => 'Các sản phẩm áp dụng chưa đạt giá trị tối thiểu ' . number_format($voucher->min_order_value, 0, ',', '.') . 'đ!']);
        }

        // 8. Tính toán số tiền được giảm dựa trên tổng tiền hợp lệ
        $discountAmount = 0;
        if ($voucher->discount_type === 'percent') { // Giảm theo %
            $discountAmount = ($eligibleTotalPrice * $voucher->discount_value) / 100;
            // Check giảm tối đa
            if ($voucher->max_discount && $discountAmount > $voucher->max_discount) {
                $discountAmount = $voucher->max_discount;
            }
        } else { // Giảm số tiền cố định (fixed)
            $discountAmount = $voucher->discount_value; 
        }

        // Không cho phép giảm lố tiền đơn hàng (chỉ giảm tối đa bằng tổng tiền sản phẩm hợp lệ)
        if ($discountAmount > $eligibleTotalPrice) {
            $discountAmount = $eligibleTotalPrice;
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

    public function getVariants(Request $request)
    {
        $productId = $request->product_id;
        $product = Product::with(['variants' => function($q) {
            $q->where('status', 'active')->with(['attributeValues.attribute']);
        }])->find($productId);
        
        if (!$product || $product->type !== 'variable') {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không có biến thể!']);
        }

        $activeVariants = $product->variants;
        $groupedAttributes = [];
        $variantsJS = [];

        foreach($activeVariants as $variant) {
            $attrIds = [];
            foreach($variant->attributeValues as $val) {
                $attrName = $val->attribute->name;
                if (!isset($groupedAttributes[$attrName])) {
                    $groupedAttributes[$attrName] = [];
                }
                $groupedAttributes[$attrName][$val->id] = $val->value;
                $attrIds[] = $val->id;
            }
            sort($attrIds);

            $variantsJS[] = [
                'id' => $variant->id,
                'attributes' => $attrIds,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'sale_price' => $variant->sale_price,
                'stock' => $variant->stock,
                'image' => $variant->thumbnail ? asset('storage/' . $variant->thumbnail) : ($product->thumbnail ? asset('storage/' . $product->thumbnail) : null),
            ];
        }

        return response()->json([
            'success' => true,
            'attributes' => $groupedAttributes,
            'variants' => $variantsJS,
            'product_name' => $product->name,
            'product_thumbnail' => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null
        ]);
    }

    public function changeVariant(Request $request)
    {
        $cartItemId = $request->item_id;
        $newVariantId = $request->variant_id;

        $cartItem = CartItem::find($cartItemId);
        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ!']);
        }

        $newVariant = ProductVariant::find($newVariantId);
        if (!$newVariant || $newVariant->status !== 'active') {
             return response()->json(['success' => false, 'message' => 'Phiên bản không khả dụng!']);
        }

        if ($newVariant->stock < $cartItem->quantity) {
             return response()->json(['success' => false, 'message' => 'Kho không đủ số lượng cho phiên bản mới!']);
        }

        $existingItem = CartItem::where('cart_id', $cartItem->cart_id)
            ->where('product_id', $cartItem->product_id)
            ->where('product_variant_id', $newVariantId)
            ->where('id', '!=', $cartItemId)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $cartItem->quantity;
            if ($existingItem->quantity > $newVariant->stock) {
                $existingItem->quantity = $newVariant->stock;
            }
            $existingItem->save();
            $cartItem->delete();
            return response()->json(['success' => true, 'message' => 'Đã cập nhật và hợp nhất sản phẩm!']);
        }

        $cartItem->product_variant_id = $newVariantId;
        $cartItem->save();

        return response()->json(['success' => true, 'message' => 'Đã thay đổi phiên bản thành công!']);
    }
}