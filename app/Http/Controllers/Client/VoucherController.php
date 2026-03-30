<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function index()
    {
        $query = Voucher::orderBy('id', 'desc');
        $savedVoucherIds = [];
        $search = request()->input('search');
        $type = request()->input('type');
        $min_spend = request()->input('min_spend');
        $user = User::find(Auth::id());

        if ($search) {
            // Nhóm Tìm kiếm: (Code LIKE ... OR Name LIKE ...)
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        if ($type == 'percent') {
            $query->where('discount_type', 'percent');
        } elseif ($type == 'fixed') {
            $query->where('discount_type', 'fixed');
        }

        if (request('min_spend')) {
            match ($min_spend) {
                '0-200k' => $query->where(function ($q) {
                    // Nhóm Min_spend: (Giá trị <= 200k OR Giá trị là NULL)
                    $q->where('min_order_value', '<=', 200000)
                        ->orWhereNull('min_order_value');
                }),
                '200k-1m' => $query->whereBetween('min_order_value', [200000, 1000000]),
                'above-1m' => $query->where('min_order_value', '>', 1000000),
            };
        }

        $vouchers = $query->paginate(10);


        // 3. Nếu user ĐÃ ĐĂNG NHẬP, lấy ra danh sách ID voucher họ đã lưu
        if (Auth::check()) {

            $savedVoucherIds = $user
                ->userVouchers()
                ->pluck('voucher_id') // Chỉ lấy cột ID
                ->toArray(); // Chuyển về mảng phẳng [1, 2, 3]

        }
        return view('client.vouchers.index')->with([
            'vouchers' => $vouchers,
            'savedVoucherIds' => $savedVoucherIds
        ]);
    }


    public function delete($id)
    {
        $user = User::find(Auth::id());
        $user->userVouchers()->detach($id);
        return back()->with('success', 'Đã bỏ lưu voucher thành công!');
    }


   public function saveVoucher($id)
    {
        $user = User::find(Auth::id());

        if ($user) {
            // KIỂM TRA: Nếu user đã lưu (hoặc đã đổi bằng điểm) voucher này rồi thì chặn lại
            $exists = \Illuminate\Support\Facades\DB::table('user_vouchers')
                        ->where('user_id', $user->id)
                        ->where('voucher_id', $id)
                        ->exists();

            if ($exists) {
                return back()->with('error', 'Bạn đã lưu voucher này rồi! Hãy vào kho Voucher để kiểm tra.');
            }

            // Nếu chưa lưu -> Tiến hành lưu
            $user->userVouchers()->attach($id);
            return back()->with('success', 'Lưu voucher thành công! Hãy vào Ví Voucher để kiểm tra.');
        } else {
            return back()->with('error', 'Bạn cần phải đăng nhập để lưu ưu đãi.');
        }
    }
}
