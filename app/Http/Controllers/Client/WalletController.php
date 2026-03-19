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
    public function createDeposit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10000']);
        $user = Auth::user();
        // dd($user->wallet->balance);
        // 1. Tạo giao dịch Pending trong DB
        $transaction = $user->wallet->transactions()->create([
            'type'           => 'deposit',
            'amount'         => $request->amount,
            'balance_before' => $user->wallet->balance,
            'balance_after' => $user->wallet->balance += $request->amount,
            'status'         => 'pending',
            'description'    => 'Nạp tiền vào ví qua VNPay',
            // Lưu ID làm mã tham chiếu gửi lên VNPay
        ]);

        // 2. Cấu hình tham số gửi lên VNPay
        $vnp_Url = env('VNPAY_URL');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => env('VNPAY_TMN_CODE'),
            "vnp_Amount"     => $transaction->amount * 100, // VNPay yêu cầu nhân số tiền với 100
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $request->ip(),
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => "Nap tien vao vi GD: " . $transaction->id,
            "vnp_OrderType"  => "billpayment",
            "vnp_ReturnUrl"  => env('VNPAY_RETURN_URL'),
            "vnp_TxnRef"     => $transaction->id, // Mã giao dịch của bạn
        ];

        // 3. Sắp xếp dữ liệu và tạo chữ ký (Signature)
        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // 4. Chuyển hướng khách sang trang VNPay
        return redirect($vnp_Url);
    }
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
                    'reference_id' => $withdrawalRequest->id,
                    'reference_type' =>  get_class($withdrawalRequest),
                    'balance_after' => $transaction->wallet->balance + $transaction->amount,
                    'description' => 'Hoàn tiền',
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
