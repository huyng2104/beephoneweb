@extends('admin.layout')

@section('title', 'Liên hệ và Hỗ trợ Bee Phone')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 lg:px-10 py-10">
    <!-- Page Heading -->
    <div class="mb-10 text-center max-w-2xl mx-auto">
        <h1 class="text-[#181611] dark:text-white text-4xl lg:text-5xl font-black leading-tight tracking-[-0.033em] mb-4">
            Chúng tôi có thể giúp gì cho bạn?
        </h1>
        <p class="text-[#8a8060] dark:text-white/60 text-lg font-normal leading-normal">
            Tìm kiếm câu trả lời nhanh chóng hoặc gửi yêu cầu hỗ trợ trực tiếp cho đội ngũ Bee Phone
        </p>
    </div>

    <!-- Search Bar -->
    <div class="max-w-3xl mx-auto mb-16">
        <div class="px-4 py-3">
            <label class="flex flex-col min-w-40 h-14 w-full shadow-lg rounded-xl overflow-hidden">
                <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
                    <div class="text-[#8a8060] flex border-none bg-white dark:bg-white/5 items-center justify-center pl-5 border-r-0">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden text-[#181611] dark:text-white focus:outline-0 focus:ring-0 border-none bg-white dark:bg-white/5 h-full placeholder:text-[#8a8060] px-4 pl-3 text-lg font-normal leading-normal" placeholder="Tìm kiếm câu hỏi thường gặp..." value="" />
                </div>
            </label>
        </div>
    </div>

    <!-- FAQ Section -->
    <section class="mb-20">
        <div class="flex items-center justify-center gap-2 mb-8">
            <span class="material-symbols-outlined text-primary">help_center</span>
            <h2 class="text-[#181611] dark:text-white text-2xl font-bold leading-tight tracking-[-0.015em]">Câu hỏi thường gặp (FAQ)</h2>
        </div>
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-4">
            <details class="flex flex-col rounded-xl border border-[#e6e3db] dark:border-white/10 bg-white dark:bg-white/5 px-5 py-4 group cursor-pointer hover:border-primary/50 transition-colors" open>
                <summary class="flex items-center justify-between gap-6 py-1 list-none">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">local_shipping</span>
                        <p class="text-[#181611] dark:text-white text-base font-semibold leading-normal">Giao hàng</p>
                    </div>
                    <span class="material-symbols-outlined text-[#181611] dark:text-white group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <p class="text-[#8a8060] dark:text-white/60 text-sm font-normal leading-relaxed pt-4">
                    Bee Phone hỗ trợ giao hàng hỏa tốc trong 2h tại Hà Nội và TP.HCM. Đối với các tỉnh thành khác, thời gian nhận hàng từ 2-4 ngày làm việc. Chúng tôi miễn phí vận chuyển cho đơn hàng trên 2 triệu đồng.
                </p>
            </details>
            <details class="flex flex-col rounded-xl border border-[#e6e3db] dark:border-white/10 bg-white dark:bg-white/5 px-5 py-4 group cursor-pointer hover:border-primary/50 transition-colors">
                <summary class="flex items-center justify-between gap-6 py-1 list-none">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">assignment_return</span>
                        <p class="text-[#181611] dark:text-white text-base font-semibold leading-normal">Đổi trả</p>
                    </div>
                    <span class="material-symbols-outlined text-[#181611] dark:text-white group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <p class="text-[#8a8060] dark:text-white/60 text-sm font-normal leading-relaxed pt-4">
                    Quý khách có thể đổi trả sản phẩm trong vòng 30 ngày nếu phát sinh lỗi từ nhà sản xuất. Sản phẩm phải còn nguyên hộp, phụ kiện và chưa qua sửa chữa bên ngoài.
                </p>
            </details>
            <details class="flex flex-col rounded-xl border border-[#e6e3db] dark:border-white/10 bg-white dark:bg-white/5 px-5 py-4 group cursor-pointer hover:border-primary/50 transition-colors">
                <summary class="flex items-center justify-between gap-6 py-1 list-none">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">verified_user</span>
                        <p class="text-[#181611] dark:text-white text-base font-semibold leading-normal">Bảo hành</p>
                    </div>
                    <span class="material-symbols-outlined text-[#181611] dark:text-white group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <p class="text-[#8a8060] dark:text-white/60 text-sm font-normal leading-relaxed pt-4">
                    Mọi sản phẩm tại Bee Phone đều được hưởng chính sách bảo hành chính hãng 12 tháng. Bạn có thể tra cứu bảo hành điện tử qua số IMEI/Serial Number ngay trên website.
                </p>
            </details>
            <details class="flex flex-col rounded-xl border border-[#e6e3db] dark:border-white/10 bg-white dark:bg-white/5 px-5 py-4 group cursor-pointer hover:border-primary/50 transition-colors">
                <summary class="flex items-center justify-between gap-6 py-1 list-none">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">payments</span>
                        <p class="text-[#181611] dark:text-white text-base font-semibold leading-normal">Thanh toán</p>
                    </div>
                    <span class="material-symbols-outlined text-[#181611] dark:text-white group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <p class="text-[#8a8060] dark:text-white/60 text-sm font-normal leading-relaxed pt-4">
                    Chúng tôi chấp nhận thanh toán qua thẻ VISA/MasterCard, chuyển khoản, ví MoMo, ZaloPay và hỗ trợ trả góp 0% lãi suất qua thẻ tín dụng hoặc các công ty tài chính.
                </p>
            </details>
        </div>
    </section>

    <!-- Contact Support Form and Info -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">
        <!-- Left Column: Support Form -->
        <div class="lg:col-span-3 bg-white dark:bg-white/5 rounded-2xl border border-[#e6e3db] dark:border-white/10 p-8 shadow-sm">
            <h3 class="text-2xl font-bold mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">mail</span>
                Gửi yêu cầu hỗ trợ
            </h3>
            <form action="{{ route('admin.support.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold">Họ và tên</label>
                        <input name="customer_name" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" placeholder="Nhập họ tên của bạn" type="text" required />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold">Email</label>
                        <input name="customer_email" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" placeholder="example@email.com" type="email" required />
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold">Số điện thoại</label>
                        <input name="customer_phone" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" placeholder="0xxx xxx xxx" type="tel" required />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold">Chủ đề cần hỗ trợ</label>
                        <select name="subject" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" required>
                            <option value="">Chọn chủ đề</option>
                            <option value="Tư vấn mua hàng">Tư vấn mua hàng</option>
                            <option value="Hỗ trợ kỹ thuật">Hỗ trợ kỹ thuật</option>
                            <option value="Khiếu nại dịch vụ">Khiếu nại dịch vụ</option>
                            <option value="Yêu cầu bảo hành">Yêu cầu bảo hành</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold">Nội dung lời nhắn</label>
                    <textarea name="message" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" placeholder="Bạn cần chúng tôi hỗ trợ gì?" rows="5" required></textarea>
                </div>
                <button class="w-full md:w-auto min-w-[200px] h-14 bg-primary text-[#181611] font-bold text-lg rounded-xl hover:opacity-90 transition-all shadow-md shadow-primary/20 flex items-center justify-center gap-2" type="submit">
                    <span class="material-symbols-outlined">send</span>
                    Gửi yêu cầu
                </button>
            </form>
        </div>

        <!-- Right Column: Direct Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e6e3db] dark:border-white/10 p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-6">Liên hệ trực tiếp</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-full bg-primary/20 text-primary">
                            <span class="material-symbols-outlined">call</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-[#8a8060] dark:text-white/40 tracking-wider">Hotline 24/7</p>
                            <p class="text-lg font-bold">1900 8888</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-full bg-primary/20 text-primary">
                            <span class="material-symbols-outlined">alternate_email</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-[#8a8060] dark:text-white/40 tracking-wider">Email hỗ trợ</p>
                            <p class="text-lg font-bold">support@beephone.vn</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-full bg-primary/20 text-primary">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-[#8a8060] dark:text-white/40 tracking-wider">Địa chỉ cửa hàng</p>
                            <p class="text-base font-medium">123 Đường Láng, Quận Đống Đa, Hà Nội</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#e6e3db] dark:bg-white/10 rounded-2xl h-64 w-full overflow-hidden relative group">
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDAdd2UHS0Gr0pyQyQ1icq1S6EaTjSiZqkkarE8LHKx5l3YZB25roFrllb49cEkJAGR7WmrJsxqAPE5f7fQENPjO4I-NEpgaSrYwSXuyOwxV-PMDPdxzGR78w8vjTP8Ig8vCAUmGn8DyQa4NCMCoqmh3ykKljG98GxWbAjo3lWYhlPoHasGG5mo3qobyKr42TQRNiYGphVj0meFPNG6VEcwRfbf8Z0UYMm3yNFC0TazqWzMOltEwFMRXjbmTdYYPy_JDRg_Kkkb5GI');"></div>
                <div class="absolute inset-0 bg-black/10 flex items-center justify-center">
                    <div class="bg-white dark:bg-background-dark px-4 py-2 rounded-lg shadow-xl flex items-center gap-2">
                        <span class="material-symbols-outlined text-red-500">location_on</span>
                        <span class="text-sm font-bold">Bee Phone Hà Nội</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed bottom-6 right-6 z-[60] flex flex-col items-end gap-3 group">
        <div class="bg-white dark:bg-background-dark shadow-2xl rounded-xl py-3 px-5 border border-[#e6e3db] dark:border-white/10 animate-bounce transition-all duration-300 transform opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0">
            <p class="text-sm font-bold text-[#181611] dark:text-white">Chat với nhân viên tư vấn 👋</p>
        </div>
        <button class="size-16 bg-primary text-[#181611] rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform relative">
            <span class="material-symbols-outlined text-3xl">chat</span>
            <span class="absolute top-0 right-0 size-4 bg-red-500 border-2 border-white dark:border-background-dark rounded-full"></span>
        </button>
    </div>

    <footer class="bg-white dark:bg-white/5 border-t border-[#f5f3f0] dark:border-white/10 mt-20 pb-10">
        <div class="max-w-[1200px] mx-auto px-4 lg:px-10 py-8 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-4">
                <div class="size-5 text-primary">
                    <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z" fill="currentColor" fill-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-[#181611] dark:text-white text-base font-bold">Bee Phone</h2>
            </div>
            <p class="text-[#8a8060] dark:text-white/40 text-sm">© 2024 Bee Phone Store. All rights reserved.</p>
            <div class="flex gap-6">
                <a class="text-[#8a8060] dark:text-white/40 hover:text-primary transition-colors" href="#">Privacy Policy</a>
                <a class="text-[#8a8060] dark:text-white/40 hover:text-primary transition-colors" href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</div>
@endsection
