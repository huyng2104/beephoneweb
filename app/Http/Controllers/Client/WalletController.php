<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    // ==================================================
    // 1. TẠO LỆNH NẠP TIỀN & ĐẨY SANG VNPAY
    // ==================================================
    public function createDeposit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10000']);
        $user = Auth::user();
        
        // 1. Tạo giao dịch Pending trong DB
        $transaction = $user->wallet->transactions()->create([
            'type'           => 'deposit',
            'amount'         => $request->amount,
            'balance_before' => $user->wallet->balance,
            'balance_after' => $user->wallet->balance += $request->amount,
            'status'         => 'pending',
            'description'    => 'Nạp tiền vào ví qua VNPay',
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
            // QUAN TRỌNG: Gọi route hứng kết quả nạp ví (Không dùng env nữa)
            "vnp_ReturnUrl"  => route('wallet.vnpay.return'), 
            "vnp_TxnRef"     => $transaction->id, // Mã giao dịch
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

    // ==================================================
    // 2. HỨNG KẾT QUẢ TỪ VNPAY VÀ CỘNG TIỀN
    // ==================================================
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $transactionId = $request->vnp_TxnRef;
        $transaction = WalletTransaction::find($transactionId);

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                // TING TING: GIAO DỊCH THÀNH CÔNG
                if ($transaction && $transaction->status == 'pending') {
                    $wallet = $transaction->wallet;
                    
                    // Cộng tiền vào ví
                    $wallet->increment('balance', $transaction->amount);

                    // Cập nhật trạng thái giao dịch
                    $transaction->update([
                        'status' => 'completed',
                        'balance_after' => $wallet->balance // Cập nhật lại số dư mới nhất
                    ]);
                }
                return redirect()->route('profile.wallet')->with('success', 'Nạp tiền vào ví thành công!');
            } else {
                // GIAO DỊCH THẤT BẠI HOẶC KHÁCH HỦY
                if ($transaction && $transaction->status == 'pending') {
                    $transaction->update([
                        'status' => 'failed', 
                        'description' => 'Nạp tiền thất bại hoặc bị hủy'
                    ]);
                }
                return redirect()->route('profile.wallet')->with('error', 'Nạp tiền thất bại hoặc đã bị hủy!');
            }
        } else {
            return redirect()->route('profile.wallet')->with('error', 'Lỗi bảo mật dữ liệu VNPAY!');
        }
    }

    // ==================================================
    // 3. TẠO YÊU CẦU RÚT TIỀN (Giữ nguyên của bro)
    // ==================================================
    public function withdrawalPost(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'required|string',
            'account_number' => 'required|numeric',
            'account_name' => 'required|string',
        ]);

        $wallet = Auth::user()->wallet;
        
        if ($wallet->balance < $request->amount) {
            return back()->with([
                'error' => 'Số tiền rút nhiều hơn số dư trong ví!'
            ]);
        }

        $hasPendingRequest = WithdrawalRequest::where('user_id', Auth::user()->id)
            ->where('status', 'pending')
            ->exists();
            
        if ($hasPendingRequest) {
            return back()->withInput()->with('error', 'Bạn đang có một lệnh rút tiền đang được xử lý. Vui lòng chờ Admin duyệt xong trước khi tạo lệnh mới!');
        }

        // 4. Lấy thông tin Ngân hàng thụ hưởng dựa trên lựa chọn
        $bankName = '';
        $accountNumber = '';
        $accountName = '';

        if ($request->bank_account_id === 'manual') {
            // Nếu nhập thủ công (lấy mã code VCB, MB... từ frontend)
            $bankName = $request->manual_bank_name;
            $accountNumber = $request->manual_account_number;
            $accountName = strtoupper($request->manual_account_name); // Ép viết hoa tên chủ thẻ
        } else {
            // Nếu chọn ngân hàng đã lưu, kiểm tra xem ID này có thuộc về user không
            $bank = $user->bankAccounts()->where('id', $request->bank_account_id)->first();

            if (!$bank) {
                return back()->with('error', 'Tài khoản ngân hàng không hợp lệ!');
            }

            $bankName = $bank->bank_code;
            $accountNumber = $bank->account_number;
            $accountName = $bank->account_name;
        }

        try {
            DB::transaction(function () use ($wallet, $request, $bankName, $accountNumber, $accountName) {

                // 1. Tạo yêu cầu rút tiền trước
                $withdrawal = WithdrawalRequest::create([
                    'user_id'        => $wallet->user->id,
                    'amount'         => $request->amount,
                    'bank_name'      => $bankName,
                    'account_number' => $accountNumber,
                    'account_name'   => $accountName,
                    'status'         => 'pending',
                ]);

                // 2. Tạo giao dịch ví và liên kết với $withdrawal->id
                $transaction = $wallet->transactions()->create([
                    'type' => 'withdraw',
                    'amount' => $request->amount,
                    'balance_before' => $wallet->balance,
                    'balance_after'  => $wallet->balance - $request->amount,
                    'description'    => 'Trừ tiền đơn rút (' . $withdrawal->id . ')',
                    'status'         => 'completed',
                    // Gán 2 cột này để Admin có thể lấy ra sau này
                    'reference_type' => WithdrawalRequest::class,
                    'reference_id'   => $withdrawal->id,
                ]);

                // 3. Cập nhật lại transaction_id cho đơn rút (Nếu bạn vẫn muốn dùng cột này)
                $withdrawal->update(['transaction_id' => $transaction->id]);

                // 4. Trừ số dư ví ngay lập tức
                $wallet->decrement('balance', $request->amount);
            });
            
            return back()->with(['success' => 'Tạo yêu cầu rút tiền thành công! Vui lòng chờ Admin xử lý.']);
        } catch (\Exception $e) {
            return back()->with(['error' => 'Đã xảy ra lỗi trong quá trình tạo lệnh rút tiền!']);
        }
    }

    // ==================================================
    // 4. HỦY YÊU CẦU RÚT TIỀN (Giữ nguyên của bro)
    // ==================================================
    public function withdrawalCancelled($id)
    {
        $withdrawalRequest = WithdrawalRequest::find($id);
        $transaction = WalletTransaction::where('id', $withdrawalRequest->transaction_id )->first();
        // Kiểm tra nếu đã hủy rồi thì không xử lý nữa (tránh cộng tiền 2 lần)
        if ($transaction->status === 'cancelled') {
            return back()->with('error', 'Giao dịch này đã được hủy trước đó.');
        }

        try {
            DB::transaction(function () use ($withdrawalRequest, $transaction) {
                // 1. Cập nhật trạng thái các bản ghi cũ
                $withdrawalRequest->update(['status' => 'canceled']);
                // $transaction->update([
                //     'status' => 'cancelled',
                //     'description' => 'Người dùng hủy lệnh rút tiền'
                // ]);

                // 2. Lấy ví và LÀM MỚI dữ liệu từ Database
                $wallet = $transaction->wallet->fresh(); // Lấy dữ liệu mới nhất từ DB

                $balanceBefore = $wallet->balance;
                $amount = $transaction->amount;
                $balanceAfter = $balanceBefore + $amount;

                // 3. Tạo bản ghi hoàn tiền với số dư CHÍNH XÁC
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'refund',
                    'amount' => $amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_id' => $withdrawalRequest->id,
                    'reference_type' => WithdrawalRequest::class,
                    'description' => 'Hoàn tiền do hủy lệnh rút',
                    'status' => 'completed'
                ]);

                // 4. Cập nhật số dư thực tế trong Database
                $wallet->increment('balance', $amount);
            });

            return back()->with('success', 'Đã hủy lệnh rút và hoàn tiền thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}