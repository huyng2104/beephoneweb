<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WithdrawalRequest;

class WalletController extends Controller
{
    public function  withdrawalPost(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'string|nullable',
            'account_number' => 'nullable|numeric',
            'bank_name' => 'string|nullable',
        ]);
        $wallet = Auth::user()->wallet;
        if ($wallet->balance <= $request->amount) {
            return back()->with([
                'error' => 'Số tiền rút nhiều hơn ví'
            ]);
        }
        $hasPendingRequest = WithdrawalRequest::where('user_id', Auth::user()->id)
            ->where('status', 'pending')
            ->exists();
        if ($hasPendingRequest) {
            return back()->with('error', 'Bạn đang có một lệnh rút tiền đang được xử lý. Vui lòng chờ Admin duyệt xong trước khi tạo lệnh mới!');
        }
        try {
            DB::transaction(function () use ($wallet, $request) {

                // Thêm record bảng transactions
                $transaction =  $wallet->transactions()->create([
                    'type' => 'withdraw',
                    'amount' => $request->amount,
                    'balance_before' => $wallet->balance,
                    'balance_after' => $wallet->balance - $request->amount,
                    'description' => 'Người dùng rút tiền',
                    'status' => 'pending',
                ]);

                // Cập số dư ví
                $wallet->balance -= $request->amount;
                $wallet->save();

                // Tạo yêu cầu rút
                WithdrawalRequest::create([
                    'user_id' => $wallet->user->id,
                    'amount' => $request->amount,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                    'transaction_id' => $transaction->id,
                ]);
            });
            return back()->with([
                'success' => 'Tạo yêu cầu rút tiền thành công'
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'error' => 'Lỗi'
            ]);
        }
    }
    public function withdrawalCancelled($id)
    {
        $transaction = WalletTransaction::findOrFail($id);
        $withdrawalRequest = WithdrawalRequest::where('transaction_id', $transaction->id)->first();

        try {
            DB::transaction(function () use ($withdrawalRequest, $transaction) {
                $withdrawalRequest->update([
                    'status' => 'canceled'
                ]);

                $transaction->update([
                    'status' => 'cancelled',
                    'description' => 'Người dùng hủy lệnh'
                ]);
                WalletTransaction::create([
                    'wallet_id' => $transaction->wallet->id,
                    'type' => 'refund',
                    'amount' => $transaction->amount,
                    'balance_before' => $transaction->wallet->balance,
                    'balance_after' => $transaction->wallet->balance + $transaction->amount,
                    'description' => 'Hoàn tiền cho lệnh rút',
                ]);
                $transaction->wallet->increment('balance', $transaction->amount);
            });
            return back()->with([
                'success' => 'Hủy lện rút thành công'
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'error' => 'Lỗi'
            ]);
        }
    }
}
