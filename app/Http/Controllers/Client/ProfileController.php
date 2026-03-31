<?php

namespace App\Http\Controllers\Client;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserClientRequest;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use  App\Models\WalletTransaction;
use App\Models\BankAccount;
use App\Models\WithdrawalRequest;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return abort(404);
        }
        return view('client.profiles.index')->with([
            'user' => $user
        ]);
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
    public function update(UpdateUserClientRequest $request, string $id)
    {
        $data = $request->except(['avatar']);
        $user = User::findOrFail($id);

        try {
            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    FileHelper::delete($user->avatar);
                }
                $data['avatar'] = FileHelper::upload($request->file('avatar'));
            }
            $user->update($data);

            return back()->with([
                'success' => 'Cập nhật thông tin thành công.'
            ]);
        } catch (\Throwable $th) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function user_wallet(Request $request)
    {

        $user = Auth::user();
        
        if (!$user) {
            abort(404);
        }
        $transactions = null;
        $banks = null;
        $wallet = Wallet::where('user_id', $user->id)->first();
        if ($wallet) {

            // Khởi tạo query giao dịch của ví này
            $query = WalletTransaction::where('wallet_id', $wallet->id)->latest();

            // 1. Lọc theo Loại giao dịch
            $query->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', $request->type);
            });

            // 2. Lọc theo Trạng thái
            $query->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
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
            $transactions = $query->paginate(5)->withQueryString();
            $banks = BankAccount::where('user_id', $wallet->user->id)->get();
        }


        return view('client.profiles.wallet')->with([
            'user' => $user,
            'transactions' => $transactions,
            'banks' => $banks,

        ]);
    }
    public function user_voucher()
    {
        $user = Auth::user();
        return view('client.profiles.voucher')->with([
            'user' => $user
        ]);
    }
    public function passwordUpdate(Request $request,   $id)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        $user = User::findOrFail($id);


        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại bạn nhập không chính xác.'
            ]);
        }
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return back()->with([
            'success' => 'Đổi mật khẩu thành công!'
        ]);
    }
    public function history_withdrawal(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if (Auth::user() && $user->id != Auth::user()->id) {
            abort(404);
        }
        // Xây dựng query cơ bản cho user này
        $query = WithdrawalRequest::where('user_id', $id);

        // Xử lý bộ lọc tìm kiếm, trạng thái, sắp xếp y như trang tổng
        if ($request->filled('search')) {
            $query->where('id', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->sort == 'oldest') {
            $query->oldest();
        } elseif ($request->sort == 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } else {
            $query->latest(); // Sắp xếp mặc định
        }

        $withdrawals = $query->paginate(10);

        // Thống kê dành riêng cho user này
        $totalPendingAmount = WithdrawalRequest::where('user_id', $id)->where('status', 'pending')->sum('amount');
        $pendingCount = WithdrawalRequest::where('user_id', $id)->where('status', 'pending')->count();
        $totalCompletedAmount = WithdrawalRequest::where('user_id', $id)->where('status', 'approved')->sum('amount');
        $rejectedCount = WithdrawalRequest::where('user_id', $id)->where('status', 'rejected')->count();

        return view('client.profiles.withdrawals', compact(
            'user',
            'withdrawals',
            'totalPendingAmount',
            'pendingCount',
            'totalCompletedAmount',
            'rejectedCount'
        ));
    }
}
