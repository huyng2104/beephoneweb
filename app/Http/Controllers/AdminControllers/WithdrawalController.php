<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('withdrawal.view');
        // 1. Lấy dữ liệu thống kê (Grid 4 cột trên cùng)
        $totalPendingAmount = WithdrawalRequest::where('status', 'pending')->sum('amount');
        $pendingCount = WithdrawalRequest::where('status', 'pending')->count();

        // Tính tổng tiền đã duyệt trong tháng hiện tại
        $totalCompletedAmount = WithdrawalRequest::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $rejectedCount = WithdrawalRequest::where('status', 'rejected')->count();
        // 2. Khởi tạo query lấy danh sách (Eager load bảng user để tránh N+1 query)
        $query = WithdrawalRequest::with('user');

        // 3. Xử lý Tìm kiếm (Theo Mã GD, Tên người dùng, Email)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Tìm theo mã giao dịch
                $q->where('transaction_id', 'like', "%{$search}%")
                    // Hoặc tìm theo tên/email người dùng thông qua relationship
                    ->orWhereHas('user', function ($qUser) use ($search) {
                        $qUser->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // 4. Xử lý Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 5. Xử lý Sắp xếp
        if ($request->sort === 'oldest') {
            $query->oldest();
        } elseif ($request->sort === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } else {
            $query->latest();
        }

        // 6. Phân trang (15 dòng/trang) và giữ lại các tham số url khi chuyển trang
        $withdrawals = $query->paginate(15)->withQueryString();

        return view('admin.withdrawals.index', compact(
            'withdrawals',
            'totalPendingAmount',
            'pendingCount',
            'totalCompletedAmount',
            'rejectedCount'
        ));
    }

    public function show($id)
    {
        Gate::authorize('withdrawal.view');
        // Lấy chi tiết đơn rút kèm thông tin user và ví của họ để đối chiếu số dư
        $withdrawal = WithdrawalRequest::with(['user.wallet'])->findOrFail($id);
        return view('admin.withdrawals.show', compact('withdrawal'));
    }
    /**
     * Chấp nhận yêu cầu rút tiền (Gợi ý code)
     */
    public function approve(Request $request, $id)
    {
        Gate::authorize('withdrawal.approve');
        $withdrawal = WithdrawalRequest::findOrFail($id);

        // Chặn nếu đơn đã được xử lý
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        // Validate dữ liệu từ Form
        $request->validate([
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'admin_note'  => 'nullable|string|max:255'
        ]);

        try {
            // Bắt đầu Transaction
            DB::beginTransaction();

            // 1. Cập nhật trạng thái đơn rút tiền
            $updateData = [
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'admin_note'  => $request->admin_note,
            ];

            // Nếu Admin có upload ảnh bill chuyển khoản
            if ($request->hasFile('proof_image')) {
                $updateData['proof_image'] = $request->file('proof_image')->store('withdrawals', 'public');
            }

            $withdrawal->update($updateData);

            $transaction = WalletTransaction::where('type', 'withdraw')
                ->where('reference_id', $withdrawal->id)
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'completed'
                ]);
            } else {
                // Thêm dòng này tạm thời để log ra lỗi nếu vẫn không tìm thấy
                // \Log::error('Không tìm thấy giao dịch ví cho đơn rút tiền ID: ' . $withdrawal->id);
            }

            // Hoàn tất Transaction
            DB::commit();

            return redirect()->route('admin.withdrawals.index')
                ->with('success', 'Đã duyệt yêu cầu rút tiền thành công!');
        } catch (\Exception $e) {
            // Nếu có lỗi ở bất kỳ bước nào, Rollback lại dữ liệu cũ
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra trong quá trình xử lý: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối yêu cầu rút tiền (Gợi ý code)
     */
    public function reject(Request $request, $id)
    {
        Gate::authorize('withdrawal.reject');
        $withdrawal = WithdrawalRequest::with('user.wallet')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        // Bắt buộc Admin phải nhập lý do khi từ chối
        $request->validate([
            'admin_note' => 'required|string|max:255'
        ], [
            'admin_note.required' => 'Vui lòng nhập lý do từ chối vào ô Ghi chú để người dùng biết.'
        ]);

        try {
            // Dùng Transaction để đảm bảo tính toàn vẹn dữ liệu
            DB::transaction(function () use ($withdrawal, $request) {

                $wallet = $withdrawal->user->wallet;

                // 1. Cập nhật trạng thái đơn rút tiền thành 'rejected'
                $withdrawal->update([
                    'status'      => 'rejected',
                    'approved_by' => auth()->id(),
                    'admin_note'  => $request->admin_note
                ]);

                // 2. Cập nhật giao dịch trừ tiền ban đầu thành 'failed'
                // $originalTransaction = WalletTransaction::where('reference_type', WithdrawalRequest::class)
                //     ->where('reference_id', $withdrawal->id)
                //     ->first();

                // if ($originalTransaction) {
                //     $originalTransaction->update([
                //         'status' => 'failed' // Khớp với model của bạn là 'Thất bại'
                //     ]);
                // }

                // 3. Xử lý hoàn tiền nếu người dùng có ví
                if ($wallet) {
                    $balanceBefore = $wallet->balance;

                    // Cộng lại tiền vào ví
                    $wallet->increment('balance', $withdrawal->amount);

                    $balanceAfter = $wallet->fresh()->balance;

                    // 4. Tạo giao dịch 'refund' để ghi nhận lịch sử cộng tiền
                    $wallet->transactions()->create([
                        'type'           => 'refund', // Sẽ được Model render là "Hoàn tiền"
                        'amount'         => $withdrawal->amount,
                        'balance_before' => $balanceBefore,
                        'balance_after'  => $balanceAfter,
                        'description'    => 'Hoàn tiền do từ chối đơn rút #' . $withdrawal->id . '. Lý do: ' . $request->admin_note,
                        'status'         => 'completed', // Hoàn tiền thành công ngay lập tức
                        'reference_type' => WithdrawalRequest::class,
                        'reference_id'   => $withdrawal->id,
                    ]);
                }
            });

            return redirect()->route('admin.withdrawals.index')
                ->with('success', 'Đã từ chối đơn và hoàn tiền lại vào ví cho khách.');
        } catch (\Exception $e) {
            // Bắt lỗi nếu có trục trặc trong quá trình DB transaction
            return back()->with('error', 'Đã xảy ra lỗi hệ thống, không thể từ chối đơn.');
        }
    }
    public function history(Request $request, $id)
    {
        Gate::authorize('withdrawal.view');
        $user = User::findOrFail($id);

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

        return view('admin.withdrawals.history', compact(
            'user',
            'withdrawals',
            'totalPendingAmount',
            'pendingCount',
            'totalCompletedAmount',
            'rejectedCount'
        ));
    }
}
