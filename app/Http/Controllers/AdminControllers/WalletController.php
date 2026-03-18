<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    // 1. Hiển thị danh sách ví của tất cả User
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Lấy danh sách user, nếu có search thì lọc theo name hoặc email
        $users = User::with('wallet')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString(); // Rất quan trọng: Giữ lại từ khóa search khi bấm sang trang 2, 3

        return view('admin.wallets.index', compact('users', 'search'));
    }

    // 2. Xử lý cộng/trừ tiền (Admin nạp/phạt tiền user)
    public function updateBalance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'action' => 'required|in:add,subtract',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            // Sử dụng DB Transaction để đảm bảo tính toàn vẹn dữ liệu
            DB::transaction(function () use ($request) {
                // TỰ ĐỘNG TẠO VÍ: Nếu user cũ chưa có ví, hệ thống sẽ tự tạo 1 ví với số dư = 0
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $request->user_id],
                    ['balance' => 0, 'status' => 'active']
                );

                // KHÓA DÒNG (Pessimistic Locking): Tránh lỗi 2 Admin cùng cộng tiền 1 lúc
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

                $amount = $request->amount;
                $balanceBefore = $wallet->balance;

                // Xử lý logic Cộng hoặc Trừ
                if ($request->action == 'add') {
                    $wallet->balance += $amount;
                    $type = 'deposit';
                    $desc = $request->description ?? 'Nạp tiền';
                } else {
                    if ($wallet->balance < $amount) {
                        throw new \Exception('Số dư ví không đủ để trừ!');
                    }
                    $wallet->balance -= $amount;
                    $type = 'withdraw';
                    $desc = $request->description ?? 'Trừ tiền';
                }

                $wallet->save();

                // Lưu lịch sử giao dịch
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => $type,
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'description' => $desc,
                    'reference_type' => get_class(Auth::user()),
                    'reference_id' => Auth::user()->id,
                    'status' => 'completed'
                ]);
            });

            return back()->with('success', 'Cập nhật số dư thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // 3. Xem lịch sử giao dịch của 1 user cụ thể
    public function history($id)
    {
        $user = User::findOrFail($id);

        // Đảm bảo user này có ví để xem lịch sử
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'status' => 'active']
        );

        // Lấy danh sách giao dịch mới nhất xếp lên đầu
        $transactions = $wallet->transactions()->paginate(15);

        return view('admin.wallets.history', compact('user', 'wallet', 'transactions'));
    }
}
