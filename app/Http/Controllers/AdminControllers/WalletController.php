<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use App\Models\User;
use App\Models\WithdrawalRequest;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $totalBalance = Wallet::sum('balance');
        $totalDeposit = WalletTransaction::where('type', 'deposit') // Giả sử có trường phân biệt loại giao dịch
            ->where('status', 'completed') // Chỉ tính các đơn nạp thành công
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
        $totalWithdraw = WithdrawalRequest::whereIn('status', ['completed', 'approved'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $lockedWalletsCount = Wallet::where('status', 'locked')->count();

        $query = Wallet::with('user'); // Load kèm thông tin User

        // Xử lý bộ lọc tìm kiếm...
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('sort_balance') && in_array($request->sort_balance, ['asc', 'desc'])) {
            $query->orderBy('balance', $request->sort_balance);
        } else {
            // Mặc định sắp xếp ví mới nhất nếu không chọn gì
            $query->orderBy('id', 'desc');
        }

        $wallets = $query->paginate(10);
        return view('admin.wallets.index', compact(
            'wallets',
            'totalBalance',
            'totalDeposit',
            'totalWithdraw',
            'lockedWalletsCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function transactions(Request $request, $id)
    {

        $wallet = Wallet::with('user')->findOrFail($id);

        // Khởi tạo query giao dịch của ví này
        $query = WalletTransaction::where('wallet_id', $wallet->id)->latest();

        // 1. Lọc theo Loại giao dịch
        $query->when($request->filled('type'), function ($q) use ($request) {
            $q->where('type', $request->type);
        });

        // 2. Lọc theo Trạng thái
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($subQ) use ($request) {
                $subQ->where('description', 'LIKE', '%' . $request->search . '%')
                ->orWhere('id', 'LIKE', '%' . $request->search . '%');
            });
        });

        // 3. Lọc từ ngày
        $query->when($request->filled('date_from'), function ($q) use ($request) {
            // Ép về đầu ngày: 00:00:00
            $q->whereDate('created_at', '>=', $request->date_from);
        });

        // 4. Lọc đến ngày
        $query->when($request->filled('date_to'), function ($q) use ($request) {
            // Ép về cuối ngày: 23:59:59
            $q->whereDate('created_at', '<=', $request->date_to);
        });

        // Lấy dữ liệu và phân trang (Giữ lại các query string cũ khi bấm qua trang 2, 3...)
        $transactions = $query->paginate(15)->withQueryString();
        $banks = BankAccount::where('user_id', $wallet->user->id)->get();

        return view('admin.wallets.transactions', compact('wallet', 'transactions', 'banks'));
    }
    public function lock(Request $request, $id)
    {
        try {
            $wallet = Wallet::with('user')->findOrFail($id);
            if ($wallet->status === 'locked') {
                return back()->with('error', 'Ví của người dùng này đã bị khóa từ trước!');
            }
            $wallet->status = 'locked';
            $wallet->lock_reason = $request->input('lock_reason', 'Khóa bởi quản trị viên hệ thống');
            $wallet->save();
            return back()->with('success', 'Đã khóa ví của tài khoản ' . $wallet->user->name . ' thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi khóa ví: ' . $e->getMessage());
        }
    }
    public function unlock($id)
    {
        try {
            $wallet = Wallet::with('user')->findOrFail($id);
            if ($wallet->status === 'active') {
                return back()->with('error', 'Ví này hiện vẫn đang hoạt động bình thường!');
            }
            $wallet->status = 'active';
            $wallet->lock_reason = null;
            $wallet->locked_until = null;
            $wallet->pin_attempts = 0;
            $wallet->save();
            return back()->with('success', 'Đã mở khóa ví cho tài khoản ' . $wallet->user->name . ' thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi mở khóa ví: ' . $e->getMessage());
        }
    }
}
