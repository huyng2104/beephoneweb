<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupportFaq;

class ChatbotFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SupportFaq::create([
            'category' => 'shipping',
            'question' => 'Phí giao hàng bao nhiêu?',
            'answer' => 'Phí giao hàng của BeePhone phụ thuộc vào khu vực và trọng lượng sản phẩm:

• Nội thành Hà Nội/TP.HCM: 30,000đ
• Các tỉnh thành khác: 50,000đ
• Miễn phí giao hàng cho đơn hàng từ 2 triệu đồng

Thời gian giao hàng: 1-3 ngày làm việc.',
            'keywords' => 'phí giao hàng, ship, vận chuyển, delivery, bao nhiêu tiền',
            'sort_order' => 10,
            'is_active' => true
        ]);

        SupportFaq::create([
            'category' => 'warranty',
            'question' => 'Bảo hành như thế nào?',
            'answer' => 'BeePhone cung cấp chính sách bảo hành 12 tháng cho tất cả sản phẩm:

• Bảo hành chính hãng cho tất cả linh kiện
• Bảo hành tại nhà cho đơn hàng từ 5 triệu đồng
• Hotline bảo hành: 1900 XXX XXX
• Website kiểm tra bảo hành: beephone.vn/warranty',
            'keywords' => 'bảo hành, warranty, bảo hiểm, sửa chữa, hỏng',
            'sort_order' => 9,
            'is_active' => true
        ]);

        SupportFaq::create([
            'category' => 'payment',
            'question' => 'Có những phương thức thanh toán nào?',
            'answer' => 'BeePhone hỗ trợ nhiều phương thức thanh toán:

• Thanh toán khi nhận hàng (COD)
• Chuyển khoản ngân hàng
• Ví điện tử (Momo, ZaloPay, ViettelPay)
• Thẻ tín dụng/ghi nợ
• Thanh toán trả góp 0% lãi suất',
            'keywords' => 'thanh toán, payment, trả tiền, COD, chuyển khoản, ví điện tử, thẻ',
            'sort_order' => 8,
            'is_active' => true
        ]);

        SupportFaq::create([
            'category' => 'return',
            'question' => 'Chính sách đổi trả như thế nào?',
            'answer' => 'BeePhone có chính sách đổi trả trong 30 ngày:

• Đổi trả miễn phí trong 7 ngày đầu
• Bảo hành 12 tháng cho tất cả sản phẩm
• Hỗ trợ kỹ thuật 24/7
• Hotline: 1900 XXX XXX',
            'keywords' => 'đổi trả, return, refund, hoàn tiền, trả lại',
            'sort_order' => 7,
            'is_active' => true
        ]);
    }
}