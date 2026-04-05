<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ChatbotFaq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        $userMessage = $request->message;

        // ==========================================
        // 1. KIỂM TRA FAQ TRƯỚC (CÁCH 1)
        // ==========================================
        $faq = $this->searchFaq($userMessage);
        if ($faq) {
            return response()->json(['reply' => $faq->answer, 'type' => 'faq']);
        }

        // ==========================================
        // 2. NẾU KHÔNG MATCH FAQ, GỌI GEMINI
        // ==========================================
        // Lấy tất cả Key trong .env vào 1 mảng
        $keys = [
            env('GEMINI_API_KEY_1'),
            env('GEMINI_API_KEY_2'),
            env('GEMINI_API_KEY_3')
        ];
        // Lọc bỏ các key rỗng
        $validKeys = array_filter($keys, fn($k) => !empty($k));

        // Nếu không có key nào, không gọi Gemini
        if (empty($validKeys)) {
            return response()->json(['reply' => 'Hệ thống AI chưa được cấu hình API chìa khóa. Vui lòng thử lại sau hoặc liên hệ quản trị.', 'type' => 'error']);
        }

        // Random bốc đại 1 key ra để xài -> Tránh quá tải 1 key
        $apiKey = $validKeys[array_rand($validKeys)];

        // ==========================================
        // 1. CẨM NANG CHÍNH SÁCH CỬA HÀNG BEEPHONE
        // (Bro có thể tự sửa lại nội dung này cho khớp với đồ án)
        // ==========================================
        $storePolicies = "
CHÍNH SÁCH BẢO HÀNH:
- Máy mới chính hãng: Bảo hành 12 tháng tại các trung tâm bảo hành của hãng trên toàn quốc. Lỗi 1 đổi 1 trong 30 ngày đầu tiên nếu có lỗi từ nhà sản xuất.
- Máy cũ/Like New: Bảo hành 6 tháng tại BeePhone. Lỗi 1 đổi 1 trong 15 ngày đầu tiên.
- Phụ kiện (sạc, cáp, tai nghe): Bảo hành 3 tháng, 1 đổi 1.
- Không bảo hành các trường hợp: Rơi vỡ, cấn móp, vào nước, tự ý tháo máy hoặc can thiệp phần mềm (Root, Jailbreak).

CHÍNH SÁCH ĐỔI TRẢ & HOÀN TIỀN:
- Khách hàng được quyền trả hàng và hoàn tiền 100% trong 7 ngày đầu nếu sản phẩm chưa bóc seal (còn nguyên tem mác).
- Nếu đã bóc seal nhưng máy không lỗi mà khách muốn trả: Thu phí chiết khấu 20% giá trị máy.
- Thời gian hoàn tiền: Từ 3 - 5 ngày làm việc qua tài khoản ngân hàng.

CHÍNH SÁCH VẬN CHUYỂN (GIAO HÀNG):
- Giao hàng Hỏa tốc: Nhận hàng trong 2 giờ (Chỉ áp dụng nội thành Hà Nội và TP.HCM), phí ship 30.000đ.
- Giao hàng Tiêu chuẩn: Giao toàn quốc từ 2 - 4 ngày. Miễn phí vận chuyển (Freeship) cho mọi đơn hàng.
- Đồng kiểm: Khách hàng được quyền kiểm tra ngoại quan gói hàng trước khi thanh toán, KHÔNG được bóc seal hộp điện thoại nếu chưa thanh toán.

CHÍNH SÁCH THANH TOÁN & TRẢ GÓP:
- Các hình thức thanh toán: Tiền mặt khi nhận hàng (COD), Chuyển khoản VNPAY, Thanh toán qua Ví Bee Pay.
- Hỗ trợ trả góp 0% qua thẻ tín dụng hoặc trả góp qua công ty tài chính (Home Credit, HD Saison) chỉ cần CCCD.

CHƯƠNG TRÌNH KHUYẾN MÃI & TÍCH ĐIỂM (BEE POINT):
- Mua hàng tích điểm: Cứ 100.000đ thanh toán thành công sẽ được tích 1 Bee Point.
- Đổi điểm: Dùng Bee Point để đổi lấy các mã giảm giá (Voucher) cực xịn trong mục 'Ví Bee Point'.
";

        // ==========================================
        // 2. PROMPT "THIẾT QUÂN LUẬT" CHO AI
        // ==========================================
        $prompt = "Bạn là trợ lý ảo chăm sóc khách hàng của hệ thống điện thoại BeePhone. Nhiệm vụ duy nhất của bạn là giải đáp các thắc mắc của khách hàng về CHÍNH SÁCH của cửa hàng.

Dưới đây là Cẩm nang chính sách của BeePhone:
\"\"\"
" . $storePolicies . "
\"\"\"

QUY TẮC TRẢ LỜI (BẮT BUỘC PHẢI TUÂN THỦ):
1. Dựa hoàn toàn vào 'Cẩm nang chính sách' bên trên để trả lời. Không được tự bịa ra chính sách khác.
2. NẾU KHÁCH HỎI VỀ SẢN PHẨM CỤ THỂ (VD: iPhone 15 giá bao nhiêu, Samsung có hàng không, Tư vấn mua máy...): BẮT BUỘC phải từ chối khéo léo và đáp rằng: 'Dạ, em là trợ lý chuyên giải đáp chính sách cửa hàng. Để xem giá và tình trạng hàng hóa, anh/chị vui lòng gõ tên máy vào thanh tìm kiếm trên Website giúp em nhé ạ! 🥰'
3. Luôn xưng hô là 'em' và gọi khách là 'anh/chị'. Thái độ cực kỳ lịch sự, thân thiện và nhiệt tình.
4. Trả lời cực kỳ NGẮN GỌN, xúc tích, đi thẳng vào vấn đề khách hỏi.
5. Tuyệt đối KHÔNG dùng các ký tự markdown như ** hay * trong câu trả lời để tránh lỗi hiển thị.

Câu hỏi của khách: " . $userMessage;

        // ==========================================
        // 3. GỌI API GEMINI 2.5 FLASH
        // ==========================================
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->failed()) {
                if ($response->status() == 429) {
                    return response()->json(['reply' => 'Dạ hiện tại lượng khách truy cập BeePhone đang quá đông, anh/chị vui lòng chờ em 10 giây rồi nhắn lại nhé ạ! 🥰']);
                }
                return response()->json(['reply' => 'Lỗi API thật sự là: ' . $response->body()]);
            }
            
            $result = $response->json();
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $botReply = $result['candidates'][0]['content']['parts'][0]['text'];
                // Dọn dẹp markdown nếu AI vẫn cố tình trả về
                $botReply = str_replace(['**', '*'], '', $botReply);
                return response()->json(['reply' => $botReply]);
            }

            return response()->json(['reply' => 'Dạ em chưa hiểu ý anh/chị lắm ạ, anh chị có thể hỏi rõ hơn về bảo hành, giao hàng hay thanh toán không ạ?']);

        } catch (\Exception $e) {
            return response()->json(['reply' => 'Hệ thống AI đang bảo trì: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // HELPER: TÌM KIẾM FAQ MATCH KEYWORD
    // ==========================================
    private function searchFaq($userMessage)
    {
        $messageLower = strtolower($userMessage);

        // Lấy tất cả FAQ active
        $faqs = ChatbotFaq::where('is_active', true)
                          ->orderBy('priority', 'desc')
                          ->get();

        foreach ($faqs as $faq) {
            // Check trong question
            if (stripos($faq->question, $userMessage) !== false) {
                return $faq;
            }

            // Check trong keywords
            if (!empty($faq->keywords)) {
                $keywords = array_map('trim', explode(',', $faq->keywords));
                foreach ($keywords as $keyword) {
                    if (stripos($messageLower, strtolower($keyword)) !== false) {
                        return $faq;
                    }
                }
            }
        }

        return null;
    }
}