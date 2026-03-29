<footer class="bg-[#181611] text-white pt-20 pb-10">
    <div class="max-w-[1440px] mx-auto px-4 md:px-10 lg:px-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <div class="space-y-6">
                <div class="flex items-center gap-2">
                    <div class="size-8 bg-primary rounded-lg flex items-center justify-center text-black">
                        <span class="material-symbols-outlined">rocket_launch</span>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight">Bee Phone</h2>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">Công nghệ hiện đại hội tụ cùng ưu đãi bùng nổ.
                    Điểm đến cho những sáng tạo mới nhất về di động và phụ kiện.</p>
                <div class="flex gap-4">
                    <a class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center hover:bg-primary hover:text-black transition-colors"
                        href="#">
                        <span class="material-symbols-outlined text-lg">public</span>
                    </a>
                    <a class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center hover:bg-primary hover:text-black transition-colors"
                        href="#">
                        <span class="material-symbols-outlined text-lg">smart_display</span>
                    </a>
                    <a class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center hover:bg-primary hover:text-black transition-colors"
                        href="#">
                        <span class="material-symbols-outlined text-lg">chat</span>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-bold mb-6">Danh mục</h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li><a class="hover:text-primary transition-colors" href="#">Điện thoại</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Máy tính bảng &amp;
                            Laptop</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Thiết bị âm thanh</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Công nghệ đeo</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Hàng tân trang</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6">Hỗ trợ</h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li><a class="hover:text-primary transition-colors" href="#">Theo dõi đơn hàng</a></li>
                    <li><a class="hover:text-primary transition-colors" href="{{ route('return-policy') }}">Giao hàng &amp; Đổi trả</a>
                    </li>
                    <li><a class="hover:text-primary transition-colors" href="#">Chương trình Thu cũ Đổi
                            mới</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#">Liên hệ với chúng tôi</a>
                    </li>
                    <li><a class="hover:text-primary transition-colors" href="#">Thông tin bảo hành</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-6">Tham gia cộng đồng Bee</h4>
                <p class="text-gray-400 text-sm mb-4">Đăng ký để nhận thông báo về các sản phẩm mới và ưu đãi độc
                    quyền dành riêng cho thành viên.</p>
                <form class="flex flex-col gap-2">
                    <input class="bg-white/5 border-none rounded-lg focus:ring-primary text-sm h-11 px-4"
                        placeholder="Nhập email của bạn" type="email" />
                    <button
                        class="bg-primary text-black h-11 rounded-lg font-bold hover:bg-primary/90 transition-colors"
                        type="button">Đăng ký ngay</button>
                </form>
            </div>
        </div>
        <div
            class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-500 text-[12px] font-medium">
            <p>© 2024 Bee Phone Inc. Bảo lưu mọi quyền.</p>
            <div class="flex gap-6 uppercase tracking-widest">
                <a class="hover:text-white transition-colors" href="{{ route('privacy-policy') }}">Chính sách bảo mật</a>
                <a class="hover:text-white transition-colors" href="{{ route('terms') }}">Điều khoản dịch vụ</a>
                <a class="hover:text-white transition-colors" href="{{ route('cookie-policy') }}">Cài đặt Cookie</a>
            </div>
        </div>
    </div>
</footer>
