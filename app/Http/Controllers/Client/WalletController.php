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

        // 1. Tạo giao dịch Pending trong DB
        $transaction = $user->wallet->transactions()->create([
            'type'           => 'deposit',
            'amount'         => $request->amount,
            'balance_before' => $user->wallet->balance,
            'balance_after' => $user->wallet->balance,
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
    // 3. TẠO YÊU CẦU RÚT TIỀN
    // ==================================================
    public function withdrawalPost(Request $request)
    {
        $user = User::find(Auth::id());

        // 1. Validate dữ liệu linh hoạt theo lựa chọn của người dùng
        // 1. Validate form (Thêm luôn kiểm tra bắt buộc nhập PIN vào đây cho xịn)
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

        // 2. Kiểm tra số dư ví
        if ($wallet->balance < $request->amount) {
            // Đẩy lỗi thẳng vào ô 'amount' của túi 'withdrawal'
            return back()->withErrors(['amount' => 'Số tiền rút nhiều hơn số dư trong ví!'], 'withdrawal')
                ->withInput();
        }

        // 3. Kiểm tra xem ví có cài PIN chưa
        if (!$wallet->wallet_pin) {
            return back()->withErrors(['wallet_pin_confirm' => 'Ví của bạn chưa được cài đặt mã PIN.'], 'withdrawal')
                ->withInput();
        }

        // 4. So sánh mã PIN nhập vào với mã băm trong DB
        if (!Hash::check($request->wallet_pin_confirm, $wallet->wallet_pin)) {
            // Ép lỗi vào túi 'withdrawal' để mở Modal
            return back()->withErrors(['wallet_pin_confirm' => 'Mã PIN giao dịch không chính xác.'], 'withdrawal')
                ->withInput($request->except('wallet_pin_confirm')); // Sửa chữ 'wallet_pin' thành 'wallet_pin_confirm' cho khớp form
        }

        // 3. Kiểm tra xem có lệnh rút nào đang chờ duyệt không
        $hasPendingRequest = WithdrawalRequest::where('user_id', $user->id)
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

            $bankName = $bank->bank_name;
            $accountNumber = $bank->account_number;
            $accountName = $bank->account_name;
        }

        // 5. Thực thi Transaction lưu vào DB
        try {
            DB::transaction(function () use ($wallet, $request, $bankName, $accountNumber, $accountName) {

                // Thêm record bảng transactions
                $transaction = $wallet->transactions()->create([
                    'type'           => 'withdraw',
                    'amount'         => $request->amount,
                    'balance_before' => $wallet->balance,
                    'balance_after'  => $wallet->balance - $request->amount,
                    'description'    => 'Người dùng yêu cầu rút tiền',
                    'status'         => 'pending',
                ]);

                // Trừ số dư ví ngay lập tức
                $wallet->decrement('balance', $request->amount);

                // Tạo yêu cầu rút với thông tin ngân hàng đã được lọc
                WithdrawalRequest::create([
                    'user_id'        => $wallet->user->id,
                    'amount'         => $request->amount,
                    'bank_name'      => $bankName,
                    'account_number' => $accountNumber,
                    'account_name'   => $accountName,
                    'transaction_id' => $transaction->id,
                ]);
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
        $transaction = WalletTransaction::findOrFail($id);
        $withdrawalRequest = WithdrawalRequest::where('transaction_id', $transaction->id)->first();

        try {
            DB::transaction(function () use ($withdrawalRequest, $transaction) {
                $withdrawalRequest->update([
                    'status' => 'canceled'
                ]);

                $transaction->update([
                    'status' => 'cancelled',
                    'description' => 'Người dùng hủy lệnh rút tiền'
                ]);

                // Hoàn tiền lại vào ví
                WalletTransaction::create([
                    'wallet_id' => $transaction->wallet->id,
                    'type' => 'refund',
                    'amount' => $transaction->amount,
                    'balance_before' => $transaction->wallet->balance,
                    'reference_id' => $withdrawalRequest->id,
                    'reference_type' => get_class($withdrawalRequest),
                    'balance_after' => $transaction->wallet->balance + $transaction->amount,
                    'description' => 'Hoàn tiền do hủy lệnh rút',
                    'status' => 'completed'
                ]);

                $transaction->wallet->increment('balance', $transaction->amount);
            });

            return back()->with(['success' => 'Đã hủy lệnh rút và hoàn tiền lại vào ví thành công!']);
        } catch (\Exception $e) {
            return back()->with(['error' => 'Có lỗi xảy ra khi hủy lệnh rút tiền!']);
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
