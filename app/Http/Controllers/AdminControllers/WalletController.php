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
    // 1. Hiển thị danh sách ví
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('wallet')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.wallets.index', compact('users', 'search'));
    }

    // 2. Xử lý cộng/trừ tiền (Chuẩn hóa Database)
    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
            'action' => 'required|in:add,subtract',
            'description' => 'required|string|max:255'
        ]);

        $amount = $request->amount;
        $userWallet = Wallet::firstOrCreate(['user_id' => $request->user_id], ['balance' => 0, 'status' => 'active']);
        $systemWallet = Wallet::firstOrCreate(['user_id' => 1], ['balance' => 0, 'status' => 'active']);

        DB::beginTransaction();
        try {
            // Khóa dòng chống trùng lặp (Pessimistic Locking)
            $userWallet = Wallet::where('id', $userWallet->id)->lockForUpdate()->first();
            $systemWallet = Wallet::where('id', $systemWallet->id)->lockForUpdate()->first();

            $userBalanceBefore = $userWallet->balance;
            $sysBalanceBefore = $systemWallet->balance;

            if ($request->action === 'add') {
                // NẠP: Trừ ví tổng, cộng ví khách
                $systemWallet->balance -= $amount;
                $userWallet->balance += $amount;

                // Log Ví Tổng (Xuất tiền)
                WalletTransaction::create([
                    'wallet_id' => $systemWallet->id, 
                    'type' => 'withdraw', // Dùng withdraw cho hợp lệ enum của DB
                    'amount' => $amount, 
                    'balance_before' => $sysBalanceBefore, 
                    'balance_after' => $systemWallet->balance, 
                    'description' => 'Chuyển tiền cho User ID ' . $request->user_id . ': ' . $request->description, 
                    'reference_type' => get_class(Auth::user()),
                    'reference_id' => Auth::user()->id,
                    'status' => 'completed'
                ]);
                
                // Log Ví Khách (Nhận tiền)
                WalletTransaction::create([
                    'wallet_id' => $userWallet->id, 
                    'type' => 'deposit', 
                    'amount' => $amount, 
                    'balance_before' => $userBalanceBefore, 
                    'balance_after' => $userWallet->balance, 
                    'description' => 'Nhận tiền hỗ trợ: ' . $request->description, 
                    'reference_type' => get_class(Auth::user()),
                    'reference_id' => Auth::user()->id,
                    'status' => 'completed'
                ]);
                
                $msg = 'Đã cộng tiền cho khách thành công!';
            } else {
                // TRỪ: Trừ ví khách, cộng lại ví tổng
                if ($userWallet->balance < $amount) {
                    throw new \Exception('Số dư ví khách hàng không đủ để trừ!');
                }
                $systemWallet->balance += $amount;
                $userWallet->balance -= $amount;

                // Log Ví Tổng (Thu hồi)
                WalletTransaction::create([
                    'wallet_id' => $systemWallet->id, 
                    'type' => 'deposit', 
                    'amount' => $amount, 
                    'balance_before' => $sysBalanceBefore, 
                    'balance_after' => $systemWallet->balance, 
                    'description' => 'Thu hồi tiền từ User ID ' . $request->user_id . ': ' . $request->description, 
                    'reference_type' => get_class(Auth::user()),
                    'reference_id' => Auth::user()->id,
                    'status' => 'completed'
                ]);
                
                // Log Ví Khách (Bị trừ)
                WalletTransaction::create([
                    'wallet_id' => $userWallet->id, 
                    'type' => 'withdraw', 
                    'amount' => $amount, 
                    'balance_before' => $userBalanceBefore, 
                    'balance_after' => $userWallet->balance, 
                    'description' => 'Bị trừ tiền: ' . $request->description, 
                    'reference_type' => get_class(Auth::user()),
                    'reference_id' => Auth::user()->id,
                    'status' => 'completed'
                ]);
                
                $msg = 'Đã trừ tiền khách thành công!';
            }

            $systemWallet->save();
            $userWallet->save();
            DB::commit();

            return back()->with('success', $msg . ' Đã đồng bộ vào Kho Bạc!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // 3. Xem lịch sử giao dịch
    public function history($id)
    {
        $user = User::findOrFail($id);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'status' => 'active']
        );

        $transactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.wallets.history', compact('user', 'wallet', 'transactions'));
    }

    // 4. Hiển thị trang Sao kê Ví Tổng
    public function systemWallet()
    {
        $systemWallet = Wallet::firstOrCreate(
            ['user_id' => 1], 
            ['balance' => 0, 'status' => 'active']
        );

        $transactions = WalletTransaction::where('wallet_id', $systemWallet->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $users = User::where('id', '!=', 1)->get();

        return view('admin.wallets.system', compact('systemWallet', 'transactions', 'users'));
    }

    // 5. Form chuyển tiền Kho Bạc
    public function addMoneyToUser(Request $request)
    {
        $request->merge(['action' => 'add']); 
        return $this->update($request);
    }
}