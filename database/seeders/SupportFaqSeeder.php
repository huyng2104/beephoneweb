<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupportFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // Shipping
            [
                'category' => 'shipping',
                'question' => 'Thời gian giao hàng là bao lâu?',
                'answer' => 'Bee Phone hỗ trợ giao hàng hỏa tốc trong 2h tại Hà Nội và TP.HCM. Đối với các tỉnh thành khác, thời gian nhận hàng từ 2-4 ngày làm việc.',
                'sort_order' => 1,
            ],
            [
                'category' => 'shipping',
                'question' => 'Phí giao hàng có tốn kém không?',
                'answer' => 'Chúng tôi miễn phí vận chuyển cho đơn hàng trên 2 triệu đồng. Đối với đơn hàng dưới 2 triệu, phí giao hàng sẽ được tính theo khu vực.',
                'sort_order' => 2,
            ],
            [
                'category' => 'shipping',
                'question' => 'Làm sao để theo dõi đơn hàng?',
                'answer' => 'Bạn có thể theo dõi đơn hàng qua số điện thoại hoặc email đã đăng ký. Chúng tôi sẽ gửi thông tin cập nhật qua SMS và email.',
                'sort_order' => 3,
            ],

            // Warranty
            [
                'category' => 'warranty',
                'question' => 'Thời hạn bảo hành là bao lâu?',
                'answer' => 'Mọi sản phẩm tại Bee Phone đều được hưởng chính sách bảo hành chính hãng 12 tháng kể từ ngày mua hàng.',
                'sort_order' => 1,
            ],
            [
                'category' => 'warranty',
                'question' => 'Cách tra cứu bảo hành như thế nào?',
                'answer' => 'Bạn có thể tra cứu bảo hành điện tử qua số IMEI/Serial Number ngay trên website Bee Phone hoặc liên hệ hotline 1900 8888.',
                'sort_order' => 2,
            ],
            [
                'category' => 'warranty',
                'question' => 'Bảo hành có mất phí không?',
                'answer' => 'Bảo hành chính hãng hoàn toàn miễn phí trong thời hạn bảo hành. Ngoài thời hạn bảo hành, chúng tôi vẫn hỗ trợ sửa chữa với chi phí hợp lý.',
                'sort_order' => 3,
            ],

            // Payment
            [
                'category' => 'payment',
                'question' => 'Các phương thức thanh toán nào được chấp nhận?',
                'answer' => 'Chúng tôi chấp nhận thanh toán qua thẻ VISA/MasterCard, chuyển khoản ngân hàng, ví MoMo, ZaloPay và các ví điện tử khác.',
                'sort_order' => 1,
            ],
            [
                'category' => 'payment',
                'question' => 'Có hỗ trợ trả góp không?',
                'answer' => 'Có, chúng tôi hỗ trợ trả góp 0% lãi suất qua thẻ tín dụng của các ngân hàng đối tác hoặc các công ty tài chính.',
                'sort_order' => 2,
            ],
            [
                'category' => 'payment',
                'question' => 'Thời hạn hoàn tiền là bao lâu?',
                'answer' => 'Chúng tôi cam kết hoàn tiền trong vòng 7 ngày làm việc nếu sản phẩm bị lỗi từ nhà sản xuất hoặc không đúng như mô tả.',
                'sort_order' => 3,
            ],

            // Return
            [
                'category' => 'return',
                'question' => 'Thời hạn đổi trả sản phẩm là bao lâu?',
                'answer' => 'Quý khách có thể đổi trả sản phẩm trong vòng 30 ngày kể từ ngày nhận hàng nếu phát sinh lỗi từ nhà sản xuất.',
                'sort_order' => 1,
            ],
            [
                'category' => 'return',
                'question' => 'Điều kiện đổi trả sản phẩm như thế nào?',
                'answer' => 'Sản phẩm phải còn nguyên hộp, phụ kiện và chưa qua sửa chữa bên ngoài. Hóa đơn mua hàng và tem bảo hành còn nguyên vẹn.',
                'sort_order' => 2,
            ],
            [
                'category' => 'return',
                'question' => 'Phí đổi trả có tốn kém không?',
                'answer' => 'Chúng tôi hỗ trợ đổi trả hoàn toàn miễn phí cho các trường hợp lỗi từ nhà sản xuất. Khách hàng chỉ cần chịu phí vận chuyển nếu lỗi do sử dụng sai cách.',
                'sort_order' => 3,
            ],
        ];

        foreach ($faqs as $faq) {
            \App\Models\SupportFaq::create($faq);
        }
    }
}
