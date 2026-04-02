<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class WalletController extends Controller
{
    // ==================================================
    // 1. TẠO LỆNH NẠP TIỀN & ĐẨY SANG VNPAY
    // ==================================================
    public function createDeposit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10000']);
        $user = Auth::user();

        // TẠO MÃ GIAO DỊCH TẠM THỜI
        // Cấu trúc: PREFIX _ USERID _ TIMESTAMP (Ví dụ: NAP_1_1712345678)
        $vnp_TxnRef = 'NAP_' . $user->id . '_' . time();

        $vnp_Url = env('VNPAY_URL');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        $inputData = [
            "vnp_Version"    => "2.1.0",
            "vnp_TmnCode"    => env('VNPAY_TMN_CODE'),
            "vnp_Amount"     => $request->amount * 100,
            "vnp_Command"    => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $request->ip(),
            "vnp_Locale"     => "vn",
            "vnp_OrderInfo"  => "Nap tien vao vi: " . $user->name,
            "vnp_OrderType"  => "billpayment",
            "vnp_ReturnUrl"  => route('wallet.vnpay.return'),
            "vnp_TxnRef"     => $vnp_TxnRef,
        ];

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

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {

                // 1. Phân tách mã vnp_TxnRef để lấy ID User (Ví dụ: NAP_1_1712345678 => 1)
                $parts = explode('_', $request->vnp_TxnRef);
                $userId = $parts[1] ?? null;

                // 2. Kiểm tra giao dịch đã tồn tại chưa bằng cách tìm trong cột description
                // Nếu description chứa mã vnp_TxnRef này nghĩa là đơn đã được cộng tiền rồi
                $alreadyExists = WalletTransaction::where('description', 'LIKE', '%' . $request->vnp_TxnRef . '%')->exists();

                if (!$alreadyExists && $userId) {
                    $user = \App\Models\User::find($userId);

                    if ($user) {
                        $wallet = $user->wallet;
                        $amount = $request->vnp_Amount / 100;

                        $balanceBefore = $wallet->balance;
                        $balanceAfter = $balanceBefore + $amount;

                        // 3. Cập nhật tiền vào ví
                        $wallet->update(['balance' => $balanceAfter]);

                        // 4. Tạo Transaction mới (Lưu vnp_TxnRef thẳng vào description để sau này dò lại)
                        WalletTransaction::create([
                            'user_id'        => $user->id,
                            'wallet_id'      => $wallet->id,
                            'type'           => 'deposit',
                            'amount'         => $amount,
                            'balance_before' => $balanceBefore,
                            'balance_after'  => $balanceAfter,
                            'status'         => 'completed',
                            'description'    => 'Nạp tiền VNPay (Mã đơn: ' . $request->vnp_TxnRef . ' - Mã NH: ' . $request->vnp_BankTrano . ')',
                        ]);

                        return redirect()->route('profile.wallet')->with('success', 'Nạp tiền vào ví thành công!');
                    }
                }

                return redirect()->route('profile.wallet')->with('info', 'Giao dịch đã được xử lý hoặc không hợp lệ.');
            } else {
                // Thất bại thì không cần tạo DB, chỉ báo lỗi cho user biết
                return redirect()->route('profile.wallet')->with('error', 'Thanh toán không thành công (Code: ' . $request->vnp_ResponseCode . ')');
            }
        } else {
            return redirect()->route('profile.wallet')->with('error', 'Lỗi bảo mật dữ liệu VNPAY!');
        }
    }

    // ==================================================
    // 3. TẠO YÊU CẦU RÚT TIỀN
    // ==================================================
    public function withdrawalPost(Request $request)
    {
        $user = User::find(Auth::id());

        // 1. Validate form
        $request->validateWithBag('withdrawal', [
            'wallet_pin_confirm'    => 'required|string|size:6',
            'amount'                => 'required|numeric|min:50000',
            'bank_account_id'       => 'required',
            'manual_bank_name'      => 'required_if:bank_account_id,manual|nullable|string',
            'manual_account_number' => 'required_if:bank_account_id,manual|nullable|string',
            'manual_account_name'   => 'required_if:bank_account_id,manual|nullable|string',
        ], [
            'wallet_pin_confirm.required'       => 'Vui lòng nhập mã PIN giao dịch.',
            'wallet_pin_confirm.size'           => 'Mã PIN phải bao gồm 6 số.',
            'amount.min'                        => 'Số tiền rút tối thiểu là 50.000 VNĐ.',
            'manual_bank_name.required_if'      => 'Vui lòng chọn ngân hàng thụ hưởng.',
            'manual_account_number.required_if' => 'Vui lòng nhập số tài khoản.',
            'manual_account_name.required_if'   => 'Vui lòng nhập tên chủ tài khoản.',
        ]);

        $wallet = $user->wallet;

        // --------------------------------------------------------------------------
        // A. KIỂM TRA TRẠNG THÁI KHÓA VÍ TRƯỚC KHI LÀM BẤT CỨ ĐIỀU GÌ
        // --------------------------------------------------------------------------
        if ($wallet->status === 'locked') {
            return back()->with('error', 'Ví của bạn đang bị khóa. Lý do: ' . $wallet->lock_reason);
        }

        // Nếu bạn có dùng tính năng khóa tạm thời (theo thời gian)
        if ($wallet->locked_until && now()->lessThan($wallet->locked_until)) {
            return back()->with('error', 'Ví của bạn đang bị khóa tạm thời đến ' . \Carbon\Carbon::parse($wallet->locked_until)->format('H:i d/m/Y'));
        }
        // --------------------------------------------------------------------------

        // 2. Kiểm tra số dư ví
        if ($wallet->balance < $request->amount) {
            return back()->withErrors(['amount' => 'Số tiền rút nhiều hơn số dư trong ví!'], 'withdrawal')
                ->withInput();
        }

        // 3. Kiểm tra xem ví có cài PIN chưa
        if (!$wallet->wallet_pin) {
            return back()->withErrors(['wallet_pin_confirm' => 'Ví của bạn chưa được cài đặt mã PIN.'], 'withdrawal')
                ->withInput();
        }

        // --------------------------------------------------------------------------
        // B. KIỂM TRA PIN & CỘNG DỒN SỐ LẦN SAI
        // --------------------------------------------------------------------------
        if (!Hash::check($request->wallet_pin_confirm, $wallet->wallet_pin)) {
            // Tăng số lần nhập sai lên 1
            $wallet->increment('pin_attempts');

            // Kiểm tra nếu sai từ 5 lần trở lên -> Khóa ví
            if ($wallet->pin_attempts >= 5) {
                $wallet->update([
                    'locked_until' => now()->addMinutes(15),
                    'status'      => 'locked',
                    'lock_reason' => 'Nhập sai mã PIN quá 5 lần.'
                ]);

                return back()->withErrors(['wallet_pin_confirm' => 'Bạn đã nhập sai mã PIN 5 lần. Ví của bạn đã bị khóa để bảo đảm an toàn!'], 'withdrawal')
                    ->withInput($request->except('wallet_pin_confirm'));
            }

            // Báo lỗi và hiển thị số lần nhập còn lại
            $remaining = 5 - $wallet->pin_attempts;
            return back()->withErrors(['wallet_pin_confirm' => "Mã PIN không chính xác. Bạn còn $remaining lần thử trước khi bị khóa ví."], 'withdrawal')
                ->withInput($request->except('wallet_pin_confirm'));
        }

        // NẾU NHẬP ĐÚNG PIN: Reset số lần nhập sai về 0
        if ($wallet->pin_attempts > 0) {
            $wallet->update(['pin_attempts' => 0]);
        }
        // --------------------------------------------------------------------------


        // 4. Kiểm tra xem có lệnh rút nào đang chờ duyệt không
        $hasPendingRequest = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return back()->withInput()->with('error', 'Bạn đang có một lệnh rút tiền đang được xử lý. Vui lòng chờ Admin duyệt xong trước khi tạo lệnh mới!');
        }

        // 5. Lấy thông tin Ngân hàng thụ hưởng dựa trên lựa chọn
        $bankName = '';
        $accountNumber = '';
        $accountName = '';

        if ($request->bank_account_id === 'manual') {
            $bankName = $request->manual_bank_name;
            $accountNumber = $request->manual_account_number;
            $accountName = strtoupper($request->manual_account_name);
        } else {
            $bank = $user->bankAccounts()->where('id', $request->bank_account_id)->first();

            if (!$bank) {
                return back()->with('error', 'Tài khoản ngân hàng không hợp lệ!');
            }

            $bankName = $bank->bank_code;
            $accountNumber = $bank->account_number;
            $accountName = $bank->account_name;
        }

        // 6. Thực thi Transaction lưu vào DB
        try {
            DB::transaction(function () use ($wallet, $request, $bankName, $accountNumber, $accountName) {

                // Tạo yêu cầu rút tiền
                $withdrawal = WithdrawalRequest::create([
                    'user_id'        => $wallet->user->id,
                    'amount'         => $request->amount,
                    'bank_name'      => $bankName,
                    'account_number' => $accountNumber,
                    'account_name'   => $accountName,
                    'status'         => 'pending',
                ]);

                // Tạo giao dịch ví
                $transaction = $wallet->transactions()->create([
                    'type'           => 'withdraw',
                    'amount'         => $request->amount,
                    'balance_before' => $wallet->balance,
                    'balance_after'  => $wallet->balance - $request->amount,
                    'description'    => 'Trừ tiền đơn rút (' . $withdrawal->id . ')',
                    'status'         => 'completed',
                    'reference_type' => WithdrawalRequest::class,
                    'reference_id'   => $withdrawal->id,
                ]);

                $withdrawal->update(['transaction_id' => $transaction->id]);

                // Trừ số dư ví ngay lập tức
                $wallet->decrement('balance', $request->amount);
            });

            return back()->with('success', 'Tạo yêu cầu rút tiền thành công! Vui lòng chờ Admin xử lý.');
        } catch (\Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi trong quá trình tạo lệnh rút tiền!');
        }
    }

    // ==================================================
    // 4. HỦY YÊU CẦU RÚT TIỀN (Giữ nguyên của bro)
    // ==================================================
    public function withdrawalCancelled($id)
    {
        $withdrawalRequest = WithdrawalRequest::find($id);
        $transaction = WalletTransaction::where('id', $withdrawalRequest->transaction_id)->first();
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

    public function active_wallet(Request $request, $id)
    {
        // dd($request->all());

        $request->validate([
            'wallet_pin'     => 'required|numeric|digits:6|confirmed',
            'bank_code'      => 'nullable|string|max:50',
            'bank_name'      => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_name'   => 'nullable|string|max:100',
        ], [
            'wallet_pin.required'  => 'Vui lòng thiết lập mã PIN ví.',
            'wallet_pin.numeric'   => 'Mã PIN ví chỉ được phép chứa các chữ số.',
            'wallet_pin.digits'    => 'Mã PIN ví phải bao gồm chính xác 6 chữ số.',
            'wallet_pin.confirmed' => 'Mã PIN xác nhận không trùng khớp. Vui lòng kiểm tra lại!',
        ]);


        $user = User::findOrFail($id);

        try {
            DB::transaction(function () use ($user, $request) {
                $user->wallet()->create([
                    'wallet_pin' => $request->wallet_pin
                ]);
                if ($request->bank_code && $request->bank_name && $request->account_number && $request->account_name) {
                    $user->bankAccounts()->create([
                        'bank_code'      => $request->bank_code,
                        'bank_name'      => $request->bank_name,
                        'account_number' => $request->account_number,
                        'account_name'   => $request->account_name,
                    ]);
                }
            });
            return back()->with([
                'success' => 'Đã kích hoạt ví'
            ]);
        } catch (\Exception $e) {
            return back()->with([
                'error' => 'Lỗi thêm DB'
            ]);
        }
    }

    public function addBankAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validateWithBag('addBank', [
            'bank_code'      => 'required|string|max:50',
            'bank_name'      => 'required|string|max:255',
            'account_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('bank_accounts', 'account_number')
                    ->where('bank_code', $request->bank_code)
                    ->where('user_id', $user->id) // Chỉ check trùng với các tài khoản CỦA USER NÀY
            ],
            'account_name'   => 'required|string|max:100',
        ], [
            'bank_code.required'      => 'Vui lòng chọn ngân hàng thụ hưởng.',
            'bank_name.required'      => 'Hệ thống không nhận diện được tên đầy đủ của ngân hàng.',
            'account_number.required' => 'Vui lòng nhập số tài khoản.',
            'account_number.unique'   => 'Số tài khoản này đã được liên kết với ngân hàng bạn chọn.', // Đã đổi lại text cho hợp lý
            'account_name.required'   => 'Vui lòng nhập tên chủ tài khoản.',
        ]);


        $user->bankAccounts()->create($validated);
        return back()->with([
            'success' => 'Đã thêm tài khoản'
        ]);
    }

    public function removeBankAccount($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->delete($id);
        return back()->with([
            'success' => 'Đã gỡ tài khoản'
        ]);
    }
}
