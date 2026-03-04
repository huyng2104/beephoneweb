@extends('admin.layouts.app')

@section('content')
    <div class="flex-1 overflow-y-auto p-8 space-y-8">
        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[#8a8060] text-sm font-medium">Tổng doanh thu</p>
                    <span class="material-symbols-outlined text-primary">payments</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white mb-1">1.250.000.000 ₫</h3>
                <div class="flex items-center gap-2">
                    <span
                        class="text-[#078812] text-xs font-bold flex items-center bg-green-50 px-2 py-0.5 rounded">+12.5%</span>
                    <span class="text-[#8a8060] text-[11px]">so với tháng trước</span>
                </div>
            </div>
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[#8a8060] text-sm font-medium">Tổng đơn hàng mới</p>
                    <span class="material-symbols-outlined text-blue-500">shopping_basket</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white mb-1">456</h3>
                <div class="flex items-center gap-2">
                    <span
                        class="text-[#078812] text-xs font-bold flex items-center bg-green-50 px-2 py-0.5 rounded">+5.2%</span>
                    <span class="text-[#8a8060] text-[11px]">từ hôm qua</span>
                </div>
            </div>
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[#8a8060] text-sm font-medium">Khách hàng mới</p>
                    <span class="material-symbols-outlined text-purple-500">person_add</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white mb-1">128</h3>
                <div class="flex items-center gap-2">
                    <span
                        class="text-[#078812] text-xs font-bold flex items-center bg-green-50 px-2 py-0.5 rounded">+8.1%</span>
                    <span class="text-[#8a8060] text-[11px]">trong tuần này</span>
                </div>
            </div>
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[#8a8060] text-sm font-medium">Sản phẩm sắp hết</p>
                    <span class="material-symbols-outlined text-red-500">warning</span>
                </div>
                <h3 class="text-2xl font-bold text-[#181611] dark:text-white mb-1">12</h3>
                <div class="flex items-center gap-2">
                    <span
                        class="text-[#e71408] text-xs font-bold flex items-center bg-red-50 px-2 py-0.5 rounded">-2.4%</span>
                    <span class="text-[#8a8060] text-[11px]">cần nhập thêm</span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Revenue Chart Area -->
            <div
                class="lg:col-span-2 bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-[#181611] dark:text-white">Biểu đồ doanh thu</h2>
                        <p class="text-sm text-[#8a8060]">Thống kê biến động trong 30 ngày qua</p>
                    </div>
                    <select
                        class="bg-[#f5f3f0] dark:bg-[#3d3725] border-none text-xs font-bold rounded-lg px-3 py-2 text-[#181611] dark:text-white focus:ring-primary/30">
                        <option>30 ngày gần nhất</option>
                        <option>Tuần này</option>
                        <option>Quý này</option>
                    </select>
                </div>
                <div class="h-64 flex flex-col gap-4">
                    <svg class="w-full h-full" fill="none" preserveaspectratio="none" viewbox="0 0 500 150">
                        <path
                            d="M0 120C20 120 40 40 60 40C80 40 100 80 120 80C140 80 160 20 180 20C200 20 220 100 240 100C260 100 280 60 300 60C320 60 340 130 360 130C380 130 400 30 420 30C440 30 460 90 480 90C500 90 500 150 500 150H0V120Z"
                            fill="url(#chartGradient)"></path>
                        <path
                            d="M0 120C20 120 40 40 60 40C80 40 100 80 120 80C140 80 160 20 180 20C200 20 220 100 240 100C260 100 280 60 300 60C320 60 340 130 360 130C380 130 400 30 420 30C440 30 460 90 480 90"
                            stroke="#f4c025" stroke-linecap="round" stroke-width="3"></path>
                        <defs>
                            <lineargradient id="chartGradient" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#f4c025" stop-opacity="0.2"></stop>
                                <stop offset="100%" stop-color="#f4c025" stop-opacity="0"></stop>
                            </lineargradient>
                        </defs>
                    </svg>
                    <div class="flex justify-between px-2 text-[11px] font-bold text-[#8a8060] tracking-wider">
                        <span>TUẦN 1</span>
                        <span>TUẦN 2</span>
                        <span>TUẦN 3</span>
                        <span>TUẦN 4</span>
                    </div>
                </div>
            </div>
            <!-- Top Products -->
            <div class="bg-white dark:bg-[#2d281a] p-6 rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm">
                <h2 class="text-lg font-bold text-[#181611] dark:text-white mb-6">Sản phẩm bán chạy</h2>
                <div class="space-y-5">
                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-lg bg-center bg-no-repeat bg-cover"
                            data-alt="iPhone 15 Pro Max product image"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAUQRypeOod-rTJ35e4VnneUwjWJkL1T_TXN8apPPGuGC5I6QBdLllfESY8_KyHK76lb40qg9MSC84fla1b-arGmNQOjGyQ5MEf0AlUPRZiuseetr-hqhGPFV6D7GPcV2M6FZMaBUGZIze6IAmt8rDIJ0ZpWub4wEpKRHOQqVYYTQOBeheB2_ik75ZDwhkS0xhKP4_SqFKf92sUToP_uQsbyyNY4r3BRnDHXFKb_AYpMx_9GlUXfmpR1hjPflCkgd4901cVo1ssYMA");'>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-[#181611] dark:text-white truncate">iPhone 15 Pro
                                Max</p>
                            <div class="w-full bg-[#f5f3f0] dark:bg-[#3d3725] h-1.5 rounded-full mt-2">
                                <div class="bg-primary h-1.5 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-[#181611] dark:text-white">1.2k</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-lg bg-center bg-no-repeat bg-cover"
                            data-alt="Samsung Galaxy S24 Ultra image"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB7AwyPoas3PhfgfnYTSlehJ2cim6_mur-g6yu7kc7S5te_MQ_GxOG6OPmVR1AGwplWIkJUebtVRR9eU9jFUr2dbccB1HdB9TwxDpjYiBvwMrfCAX25rT7YhW4sZeXEBmq_JadCgkVfs3qXb0mD9q0VA8CGBpi4xSQ2NtBC59Br9nH1vhgQ818K_1llI744NlsS-0fiNXcecajvEqb3kJ2EZ4RUCTFPKfvAAKjolEEZTLf_4Qljmfhu-ZQhXbiCULs-N4ogCKw4Wt4");'>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-[#181611] dark:text-white truncate">Samsung S24
                                Ultra</p>
                            <div class="w-full bg-[#f5f3f0] dark:bg-[#3d3725] h-1.5 rounded-full mt-2">
                                <div class="bg-primary h-1.5 rounded-full" style="width: 72%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-[#181611] dark:text-white">982</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-lg bg-center bg-no-repeat bg-cover"
                            data-alt="Xiaomi 14 Pro product image"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuABpNrMue8Kz-AyVFq8KHIyfTHKrKjpqzikJECfSyEb6WcWqSpqiHKFQoyNyBDnIHzf7z-fSWnLh7aZDGRMFcTyKWN5k1DaYQx80B9zavjh9-48K1LV7ARTZq_nI1aT1eQDP2e4InLw6Her6FylRiFXsMC0GjQAIG1J0G9fe6HGaZJsExL3qfQp67Zg8DeHC0YVG_TZCBY9CkKw2mS-3Xp1zECrzGFKj9lNvU1nBglPJJ77uFyYih9m8mY7fxonZKDny9IU-IB4wTE");'>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-[#181611] dark:text-white truncate">Xiaomi 14 Pro
                            </p>
                            <div class="w-full bg-[#f5f3f0] dark:bg-[#3d3725] h-1.5 rounded-full mt-2">
                                <div class="bg-primary h-1.5 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-[#181611] dark:text-white">645</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-12 rounded-lg bg-center bg-no-repeat bg-cover"
                            data-alt="Google Pixel 8 product image"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCLATm5TcwNz8ez-IsGgGI22MbK36EAobPHf0SgruhPs6nU1qalBVIkgVKd8fEj-sB3b4-Pbsbg9hW9z1TIFZDbAMyDY8ZC2t5U4jDMU0bDkzXqrnfcTg91ZRnQXMn-r9bJzuOvv5rv8G1C1rJJpw9kOS29ww_j8lwSh_aQ0V2nX9Z-P1kjOuxS3OLSf9Gus8kn3Izz2n2ws8LueGA5ZvtEj1OXjdWknSBFGpasID5fvfgmJrBgyLmo8YLSKChmnk4wrMrF7QTpBaw");'>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-[#181611] dark:text-white truncate">Google Pixel 8
                            </p>
                            <div class="w-full bg-[#f5f3f0] dark:bg-[#3d3725] h-1.5 rounded-full mt-2">
                                <div class="bg-primary h-1.5 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-[#181611] dark:text-white">420</span>
                    </div>
                </div>
                <button
                    class="w-full mt-6 py-2.5 rounded-lg border border-[#e6e3db] dark:border-[#3d3725] text-sm font-bold text-[#181611] dark:text-white hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    Xem tất cả báo cáo
                </button>
            </div>
        </div>
        <!-- Recent Orders Table -->
        <div
            class="bg-white dark:bg-[#2d281a] rounded-xl border border-[#e6e3db] dark:border-[#3d3725] shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#e6e3db] dark:border-[#3d3725] flex justify-between items-center">
                <h2 class="text-lg font-bold text-[#181611] dark:text-white">Đơn hàng mới nhất</h2>
                <button class="text-primary text-sm font-bold hover:underline">Xem tất cả</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-[#fcfcfc] dark:bg-[#342f1f] border-b border-[#e6e3db] dark:border-[#3d3725]">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-[#8a8060] uppercase tracking-wider">Mã
                                đơn</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#8a8060] uppercase tracking-wider">
                                Khách hàng</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#8a8060] uppercase tracking-wider">
                                Ngày tạo</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#8a8060] uppercase tracking-wider">
                                Trạng thái</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#8a8060] uppercase tracking-wider text-right">
                                Tổng cộng</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e6e3db] dark:divide-[#3d3725]">
                        <tr class="hover:bg-[#fcfcfc] dark:hover:bg-white/5">
                            <td class="px-6 py-4 text-sm font-bold text-primary">#BP-8842</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-[#181611] dark:text-white">Nguyễn Văn An</p>
                                <p class="text-[11px] text-[#8a8060]">an.nguyen@email.com</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#5e5a4d] dark:text-gray-400">14:25, 20/05</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">Đang
                                    xử lý</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-[#181611] dark:text-white text-right">
                                32.490.000 ₫</td>
                        </tr>
                        <tr class="hover:bg-[#fcfcfc] dark:hover:bg-white/5">
                            <td class="px-6 py-4 text-sm font-bold text-primary">#BP-8841</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-[#181611] dark:text-white">Trần Thị Bé</p>
                                <p class="text-[11px] text-[#8a8060]">be.tran@email.com</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#5e5a4d] dark:text-gray-400">12:10, 20/05</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Hoàn
                                    thành</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-[#181611] dark:text-white text-right">
                                18.990.000 ₫</td>
                        </tr>
                        <tr class="hover:bg-[#fcfcfc] dark:hover:bg-white/5">
                            <td class="px-6 py-4 text-sm font-bold text-primary">#BP-8840</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-[#181611] dark:text-white">Lê Hoàng Nam</p>
                                <p class="text-[11px] text-[#8a8060]">nam.lh@email.com</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#5e5a4d] dark:text-gray-400">09:45, 20/05</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-700 uppercase">Chờ
                                    thanh toán</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-[#181611] dark:text-white text-right">
                                24.500.000 ₫</td>
                        </tr>
                        <tr class="hover:bg-[#fcfcfc] dark:hover:bg-white/5">
                            <td class="px-6 py-4 text-sm font-bold text-primary">#BP-8839</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-[#181611] dark:text-white">Phạm Minh Tuấn</p>
                                <p class="text-[11px] text-[#8a8060]">tuan.pm@email.com</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#5e5a4d] dark:text-gray-400">21:30, 19/05</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 uppercase">Đã
                                    hủy</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-[#181611] dark:text-white text-right">
                                12.200.000 ₫</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
