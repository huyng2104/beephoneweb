<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTransaction;

class PaymentController extends Controller
{


    public function vnpay_response(Request $request)
    {
        $inputData = $request->all();
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        // 1. Loại bỏ các tham số chữ ký khỏi mảng để tính toán lại hash
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        // 2. Sắp xếp lại dữ liệu theo thứ tự a-z
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

        // 3. Tạo lại chữ ký để đối chiếu với chữ ký VNPay gửi sang
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra chữ ký hợp lệ
        if ($secureHash == $vnp_SecureHash) {
            $transactionId = $inputData['vnp_TxnRef'];
            $vnp_Amount = $inputData['vnp_Amount'] / 100; // VNPay nhân 100 nên phải chia lại

            // Tìm giao dịch trong DB (Bạn thay đổi query cho phù hợp với cấu trúc của mình)
            $transaction = WalletTransaction::with('wallet')->find($transactionId);

            if ($transaction != null) {
                // Kiểm tra số tiền có khớp không
                if ($transaction->amount == $vnp_Amount) {
                    // Kiểm tra trạng thái giao dịch (chỉ xử lý nếu đang là pending)
                    if ($transaction->status == 'pending') {

                        // Kiểm tra mã phản hồi của VNPay (00 = Thành công)
                        if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {

                            // Dùng Database Transaction để đảm bảo tính toàn vẹn dữ liệu
                            DB::beginTransaction();
                            try {
                                // Cập nhật trạng thái giao dịch thành công
                                $transaction->status = 'completed';
                                $transaction->save();

                                // Cộng tiền vào ví của User
                                $wallet = $transaction->wallet;
                                $wallet->balance += $transaction->amount;
                                $wallet->save();

                                DB::commit();

                                // Trả về cho VNPay biết là đã xử lý thành công
                                return view('client.payments.return_deposit')->with([
                                    'message' => 'Nạp tiền thành công',
                                    'amount' => $transaction->amount,
                                    'id_transaction' => $transactionId,
                                    'label' => 'success'
                                ]);
                            } catch (\Exception $e) {
                                DB::rollBack();
                                return view('client.payments.return_deposit')->with([
                                    'message' => 'Lỗi giao dịch',
                                    'amount' => $transaction->amount,
                                    'id_transaction' => $transactionId,
                                    'label' => 'error'
                                ]);
                            }
                        } else {
                            // Trường hợp giao dịch thất bại (khách hủy, không đủ tiền...)
                            $transaction->status = 'failed';
                            $transaction->description = 'Người dùng hủy giao dịch';
                            $transaction->save();
                            return view('client.payments.return_deposit')->with([
                                'message' => 'Giao dịch thất bại',
                                'amount' => $transaction->amount,
                                'id_transaction' => $transactionId,
                                'label' => 'error'
                            ]);
                        }
                    } else {
                        return view('client.payments.return_deposit')->with([
                            'message' => 'Giao dịch thất bại',
                            'amount' => $transaction->amount,
                            'id_transaction' => $transactionId,
                            'label' => 'error'
                        ]);
                    }
                } else {
                    return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
                }
            } else {
                return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
            }
        } else {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }
    }
}
