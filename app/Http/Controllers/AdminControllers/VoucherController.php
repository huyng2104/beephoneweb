<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoucherRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\Order;

class VoucherController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('voucher.view');
        // 1. Tổng mã đang chạy (Giả định trạng thái active)
        $now = now();

        $totalActive = Voucher::where(function ($q) use ($now) {
            $q->where('status', '!=', 0) // 1. Không bị tạm dừng (status != 0)
                ->where(function ($sub) {
                    // 2. Còn lượt dùng (hoặc không giới hạn lượt)
                    $sub->whereNull('usage_limit')
                        ->orWhereColumn('used_count', '<', 'usage_limit');
                })
                ->where(function ($sub) use ($now) {
                    // 3. Đã đến ngày bắt đầu (hoặc không có ngày bắt đầu)
                    $sub->whereNull('start_date')
                        ->orWhere('start_date', '<=', $now);
                })
                ->where(function ($sub) use ($now) {
                    // 4. Chưa qua ngày kết thúc (hoặc không có ngày kết thúc)
                    $sub->whereNull('end_date')
                        ->orWhere('end_date', '>=', $now);
                });
        })->count();
        // 2. Lượt sử dụng 30 ngày qua
        $startDate30Days = now()->subDays(30);
        $totalUsage30Days = Order::whereNotNull('voucher_id')
            ->where('created_at', '>=', $startDate30Days)
            ->count();

        // 3. Tổng tiền tiết kiệm cho khách (Giả sử bảng Order lưu số tiền giảm)
        $totalSaved = Order::whereNotNull('voucher_id')
            ->where('created_at', '>=', $startDate30Days)
            ->sum('discount_amount');

        // 4. Xử lý Biểu đồ 7 ngày qua
        // 4. Xử lý Biểu đồ 7 ngày qua
        $chartData = [];
        $maxCount = 0;

        // VÒNG LẶP 1: Gom dữ liệu 7 ngày và tìm ngày có lượt dùng cao nhất
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            // Đếm số lượt dùng trong ngày đó (Nhớ đổi Model Order thành Model của bạn nếu khác)
            $count = Order::whereNotNull('voucher_id')
                ->whereDate('created_at', $date->toDateString())
                ->count();

            // Cập nhật đỉnh ($maxCount)
            if ($count > $maxCount) {
                $maxCount = $count;
            }

            $chartData[] = [
                'day_short' => $date->translatedFormat('D'), // T2, T3...
                'day_full' => $date->translatedFormat('l'), // Thứ hai, Thứ ba...
                'count' => $count,
                'height' => 0 // Lát nữa tính sau
            ];
        }

        // 👉 ĐÂY LÀ CHỖ THÊM VÀO: Ép mức trần tối thiểu là 10
        // Nằm CÙNG CẤP, ngay giữa 2 vòng lặp bạn nhé!
        if ($maxCount < 10) {
            $maxCount = 10;
        }

        // VÒNG LẶP 2: Tính phần trăm chiều cao cho biểu đồ
        foreach ($chartData as &$data) {
            // Chia tỉ lệ % dựa trên maxCount
            $data['height'] = $maxCount > 0 ? ($data['count'] / $maxCount) * 100 : 0;

            // Nếu có lượt dùng nhưng cột lùn quá (dưới 5%) thì ép nó cao 5% cho dễ nhìn
            if ($data['height'] > 0 && $data['height'] < 5) {
                $data['height'] = 5;
            }
        }
        $query = Voucher::orderBy('id', 'desc');

        if (request('deleted') == 'trash') {
            $query->onlyTrashed();
        }

        if (request()->filled('search')) {
            $search = request('search');

            // 🔥 BẮT BUỘC dùng function($q) để bọc các điều kiện OR lại thành 1 cụm ( ... AND (A OR B) )
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")       // Tìm theo mã Voucher
                    ->orWhere('name', 'like', "%{$search}%")     // Hoặc tìm theo tên Voucher
                    ->orWhere('id', $search);                    // Hoặc tìm chính xác theo ID
            });
        }
        // 3. Xử lý bộ lọc Trạng thái (Dịch từ Accessor sang SQL)
        if (request()->filled('status')) {
            $status = request('status');
            $now = now();

            switch ($status) {
                case 'paused':
                    // status = 0
                    $query->where('status', 0);
                    break;

                case 'out_of_usage':
                    // used_count == usage_limit (Nên dùng >= cho chắc chắn)
                    $query->whereColumn('used_count', '>=', 'usage_limit');
                    break;

                case 'pending':
                    // start_date > now
                    $query->whereNotNull('start_date')->where('start_date', '>', $now);
                    break;

                case 'expired':
                    // end_date < now
                    $query->whereNotNull('end_date')->where('end_date', '<', $now);
                    break;

                case 'active':
                    // Đang hoạt động = KHÔNG lọt vào bất kỳ trường hợp nào ở trên
                    $query->where(function ($q) use ($now) {
                        $q->where('status', '!=', 0) // Không bị tạm dừng
                            ->where(function ($sub) { // Còn lượt dùng (hoặc không giới hạn lượt)
                                $sub->whereNull('usage_limit')
                                    ->orWhereColumn('used_count', '<', 'usage_limit');
                            })
                            ->where(function ($sub) use ($now) { // Đã đến ngày bắt đầu (hoặc không có ngày bắt đầu)
                                $sub->whereNull('start_date')
                                    ->orWhere('start_date', '<=', $now);
                            })
                            ->where(function ($sub) use ($now) { // Chưa qua ngày kết thúc (hoặc không có ngày kết thúc)
                                $sub->whereNull('end_date')
                                    ->orWhere('end_date', '>=', $now);
                            });
                    });
                    break;
            }
        }
        $vouchers = $query->paginate(10);

        return view('admin.vouchers.index', [
            'vouchers' => $vouchers,
            'totalActive' => $totalActive,
            'totalUsage30Days' => $totalUsage30Days,
            'totalSaved' => $totalSaved,
            'chartData' => $chartData
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('voucher.create');
        $brands = Brand::all();
        $products = Product::all();
        $categories = Category::all();
        return view('admin.vouchers.create')->with([
            'brands' => $brands,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVoucherRequest $request)
    {
        $data = [
            'name' => $request->name,
            'code' => str_replace(' ', '', $request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount' => $request->max_discount,
            'min_order_value' => $request->min_order_value,
            'usage_limit' => $request->usage_limit,
            'usage_limit_per_user' => 1,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->has('status') ? 1 : 0,
            'points_required' => $request->points_required ?? 0,
        ];
        $brandIds = $request->brands;
        $categoryIds = $request->categories;
        $productIds = $request->products;
        try {

            DB::transaction(function () use ($data, $brandIds, $categoryIds, $productIds) {

                $voucher = Voucher::create($data);
                $voucher->brands()->sync($brandIds);
                $voucher->categories()->sync($categoryIds);
                $voucher->products()->sync($productIds);
            });

            return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được tạo thành công');
        } catch (\Exception $e) {

            return back()->with('error', 'Lỗi thêm mới!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Gate::authorize('voucher.view');
        $voucher = Voucher::findOrFail($id);
        $products = Product::all();
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.vouchers.show')->with([
            'voucher' => $voucher,
            'brands' => $brands,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        Gate::authorize('voucher.update');
        $voucher = Voucher::findOrFail($id);
        $products = Product::all();
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.vouchers.edit')->with([
            'voucher' => $voucher,
            'brands' => $brands,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreVoucherRequest $request, string $id)
    {
        try {

            DB::transaction(function () use ($request, $id) {
                $voucher = Voucher::findOrFail($id);
                $voucher->update($request->validated());
                $voucher->brands()->sync($request->brands ?? []);
                $voucher->categories()->sync($request->categories ?? []);
                $voucher->products()->sync($request->products ?? []);
            });

            return redirect()->route('admin.vouchers.index')->with('success', 'Voucher đã được sửa thành công');
        } catch (\Exception $e) {

            return back()->with('error', 'Lỗi sửa!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('voucher.delete');
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();
        return back()->with([
            'success' => 'Đã xóa'
        ]);
    }
    public function restore($id)
    {
        Gate::authorize('voucher.delete');
        $voucher = Voucher::withTrashed()->findOrFail($id);
        $voucher->restore();
        return back()->with('success', 'Đã khôi phục');
    }
}