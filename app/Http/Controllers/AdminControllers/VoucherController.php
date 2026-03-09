<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoucherRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Voucher::orderBy('id', 'desc');

        if (request('deleted') == 'trash') {
            $query->onlyTrashed();
        }

        $vouchers = $query->paginate(10);

        return view('admin.vouchers.index', [
            'vouchers' => $vouchers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
            'usage_limit_per_user' => $request->usage_limit_per_user,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status ?? 0,
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
        $data = [
            'name' => $request->name,
            'code' => str_replace(' ', '', $request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount' => $request->max_discount,
            'min_order_value' => $request->min_order_value,
            'usage_limit' => $request->usage_limit,
            'usage_limit_per_user' => $request->usage_limit_per_user,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status ?? 0,
        ];
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
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();
        return back()->with([
            'success' => 'Đã xóa'
        ]);
    }
    public function restore($id)
    {
        $voucher = Voucher::withTrashed()->findOrFail($id);
        $voucher->restore();
        return back()->with('success', 'Đã khôi phục');
    }
}
