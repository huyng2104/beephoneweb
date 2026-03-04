@extends('admin.layouts.app')
@section('content')
            <!-- Header Section -->
            <header class="bg-white dark:bg-gray-900 border-b border-[#e6e3db] px-8 py-6">
                <div class="flex flex-wrap justify-between items-end gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-[#181611] dark:text-white text-3xl font-black tracking-tight">Quản lý khuyến mãi
                        </h2>
                        <p class="text-[#8a8060] text-sm font-normal">Theo dõi và cấu hình các chương trình ưu đãi của
                            Bee Phone.</p>
                    </div>
                    <button
                        class="flex items-center gap-2 rounded-lg h-11 px-6 bg-primary text-[#181611] text-sm font-bold shadow-sm hover:scale-[1.02] active:scale-95 transition-all">
                        <span class="material-symbols-outlined">add_circle</span>
                        <span>Tạo mã mới</span>
                    </button>
                </div>
            </header>
            <div class="p-8 flex flex-col gap-8">
                <!-- Stats & Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Stat Cards -->
                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div
                            class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                            <p class="text-[#8a8060] text-sm font-medium">Tổng mã đang chạy</p>
                            <p class="text-[#181611] dark:text-white text-3xl font-bold">12</p>
                            <div
                                class="flex items-center gap-1 text-[#078812] text-xs font-bold bg-[#078812]/10 px-2 py-0.5 rounded w-fit">
                                <span class="material-symbols-outlined text-sm">trending_up</span>
                                <span>+5%</span>
                            </div>
                        </div>
                        <div
                            class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                            <p class="text-[#8a8060] text-sm font-medium">Lượt sử dụng (30 ngày)</p>
                            <p class="text-[#181611] dark:text-white text-3xl font-bold">1,250</p>
                            <div
                                class="flex items-center gap-1 text-[#078812] text-xs font-bold bg-[#078812]/10 px-2 py-0.5 rounded w-fit">
                                <span class="material-symbols-outlined text-sm">trending_up</span>
                                <span>+12%</span>
                            </div>
                        </div>
                        <div
                            class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                            <p class="text-[#8a8060] text-sm font-medium">Tiết kiệm cho khách</p>
                            <p class="text-[#181611] dark:text-white text-3xl font-bold">45.0M</p>
                            <div
                                class="flex items-center gap-1 text-[#e71408] text-xs font-bold bg-[#e71408]/10 px-2 py-0.5 rounded w-fit">
                                <span class="material-symbols-outlined text-sm">trending_down</span>
                                <span>-2%</span>
                            </div>
                        </div>
                    </div>
                    <!-- Small Bar Chart -->
                    <div class="flex flex-col gap-4 rounded-xl p-6 bg-white dark:bg-gray-900 border border-[#e6e3db]">
                        <div class="flex justify-between items-start">
                            <p class="text-[#181611] dark:text-white text-sm font-bold">Lượt dùng theo ngày</p>
                            <p class="text-[#8a8060] text-xs">7 ngày qua</p>
                        </div>
                        <div class="flex items-end justify-between h-32 gap-2 px-1">
                            <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t"
                                style="height: 40%;" title="Thứ 2"></div>
                            <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t"
                                style="height: 60%;" title="Thứ 3"></div>
                            <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t"
                                style="height: 30%;" title="Thứ 4"></div>
                            <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t"
                                style="height: 95%;" title="Thứ 5"></div>
                            <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t"
                                style="height: 20%;" title="Thứ 6"></div>
                            <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t"
                                style="height: 70%;" title="Thứ 7"></div>
                            <div class="bg-primary/20 hover:bg-primary transition-colors w-full rounded-t"
                                style="height: 85%;" title="Chủ nhật"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-[#8a8060] font-bold">
                            <span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span><span>T7</span><span>CN</span>
                        </div>
                    </div>
                </div>
                <!-- Table Section -->
                <div
                    class="bg-white dark:bg-gray-900 border border-[#e6e3db] rounded-xl overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-[#e6e3db] flex flex-wrap justify-between items-center gap-4">
                        <h3 class="text-lg font-bold text-[#181611] dark:text-white">Danh sách mã giảm giá</h3>
                        <div class="flex gap-2">
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060] text-lg">search</span>
                                <input
                                    class="pl-10 pr-4 py-2 bg-background-light dark:bg-gray-800 border-none rounded-lg text-sm w-64 focus:ring-2 focus:ring-primary"
                                    placeholder="Tìm mã..." type="text" />
                            </div>
                            <button
                                class="flex items-center gap-2 px-4 py-2 border border-[#e6e3db] rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-sm">filter_list</span>
                                <span>Bộ lọc</span>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-background-light dark:bg-gray-800 text-[#8a8060] text-xs uppercase tracking-wider font-bold">
                                    <th class="px-6 py-4">Mã Code</th>
                                    <th class="px-6 py-4">Loại giảm giá</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4">Sử dụng / Tổng</th>
                                    <th class="px-6 py-4 text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e6e3db]">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-[#181611] dark:text-white">BEEIPHONE15</span>
                                            <span class="text-xs text-[#8a8060]">Mừng ra mắt iPhone 15</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded">Giảm
                                            10%</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="size-2 rounded-full bg-green-500"></div>
                                            <span class="text-sm font-medium text-green-600">Đang chạy</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1.5 w-40">
                                            <div class="flex justify-between text-xs font-bold">
                                                <span>84 / 100</span>
                                                <span>84%</span>
                                            </div>
                                            <div class="w-full bg-[#f5f3f0] h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-primary h-full" style="width: 84%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                class="p-2 hover:bg-gray-100 rounded-lg text-[#8a8060] hover:text-primary transition-colors"
                                                title="Sửa">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <button
                                                class="p-2 hover:bg-red-50 rounded-lg text-[#8a8060] hover:text-red-500 transition-colors"
                                                title="Xóa">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-[#181611] dark:text-white">HELLO2024</span>
                                            <span class="text-xs text-[#8a8060]">Khuyến mãi năm mới</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 bg-purple-50 text-purple-600 text-xs font-bold rounded">Giảm
                                            200.000đ</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="size-2 rounded-full bg-red-500"></div>
                                            <span class="text-sm font-medium text-red-600">Đã hết hạn</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1.5 w-40">
                                            <div class="flex justify-between text-xs font-bold">
                                                <span>50 / 50</span>
                                                <span>100%</span>
                                            </div>
                                            <div class="w-full bg-[#f5f3f0] h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-red-400 h-full" style="width: 100%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                class="p-2 hover:bg-gray-100 rounded-lg text-[#8a8060] transition-colors"><span
                                                    class="material-symbols-outlined text-lg">edit</span></button>
                                            <button
                                                class="p-2 hover:bg-red-50 rounded-lg text-[#8a8060] transition-colors"><span
                                                    class="material-symbols-outlined text-lg">delete</span></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-[#181611] dark:text-white">FREESHIP</span>
                                            <span class="text-xs text-[#8a8060]">Miễn phí vận chuyển toàn quốc</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 bg-green-50 text-green-600 text-xs font-bold rounded">Freeship</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="size-2 rounded-full bg-yellow-500"></div>
                                            <span class="text-sm font-medium text-yellow-600">Tạm dừng</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1.5 w-40">
                                            <div class="flex justify-between text-xs font-bold">
                                                <span>12 / 500</span>
                                                <span>2.4%</span>
                                            </div>
                                            <div class="w-full bg-[#f5f3f0] h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-primary h-full" style="width: 2.4%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                class="p-2 hover:bg-gray-100 rounded-lg text-[#8a8060] transition-colors"><span
                                                    class="material-symbols-outlined text-lg">edit</span></button>
                                            <button
                                                class="p-2 hover:bg-red-50 rounded-lg text-[#8a8060] transition-colors"><span
                                                    class="material-symbols-outlined text-lg">delete</span></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-[#e6e3db] flex justify-between items-center bg-gray-50/50">
                        <p class="text-xs text-[#8a8060]">Hiển thị 3 trong tổng số 12 mã</p>
                        <div class="flex gap-1">
                            <button
                                class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611] transition-all disabled:opacity-50"
                                disabled="">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </button>
                            <button
                                class="size-8 flex items-center justify-center rounded border border-primary bg-primary text-[#181611] font-bold text-xs">1</button>
                            <button
                                class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611] font-bold text-xs transition-all">2</button>
                            <button
                                class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611] font-bold text-xs transition-all">3</button>
                            <button
                                class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611] transition-all">
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Create/Edit Promotion Section -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 pb-20">
                    <div class="xl:col-span-2 flex flex-col gap-6">
                        <div class="bg-white dark:bg-gray-900 border border-[#e6e3db] rounded-xl p-8">
                            <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">add_circle</span>
                                Chi tiết mã giảm giá mới
                            </h3>
                            <form class="space-y-8">
                                <!-- Basic Info -->
                                <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex flex-col gap-2">
                                        <label class="text-sm font-bold text-[#181611]">Tên chương trình *</label>
                                        <input
                                            class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                            placeholder="Ví dụ: Sale hè rực rỡ" type="text" />
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class="text-sm font-bold text-[#181611]">Mã Code *</label>
                                        <div class="flex gap-2">
                                            <input
                                                class="flex-1 border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary uppercase font-mono font-bold"
                                                placeholder="SUMMER24" type="text" />
                                            <button
                                                class="px-3 bg-gray-100 rounded-lg text-xs font-bold text-gray-500 hover:bg-gray-200 transition-colors uppercase"
                                                type="button">Ngẫu nhiên</button>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2 flex flex-col gap-2">
                                        <label class="text-sm font-bold text-[#181611]">Mô tả</label>
                                        <textarea class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                            placeholder="Nội dung hiển thị cho khách hàng..." rows="2"></textarea>
                                    </div>
                                </section>
                                <hr class="border-[#e6e3db]" />
                                <!-- Conditions -->
                                <section class="space-y-6">
                                    <h4 class="text-sm font-bold uppercase tracking-wider text-[#8a8060]">Điều kiện
                                        &amp; Giá trị</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="flex flex-col gap-2">
                                            <label class="text-sm font-bold text-[#181611]">Loại giảm giá</label>
                                            <select
                                                class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary">
                                                <option>Phần trăm (%)</option>
                                                <option>Số tiền cố định (VNĐ)</option>
                                                <option>Miễn phí vận chuyển</option>
                                            </select>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="text-sm font-bold text-[#181611]">Giá trị giảm</label>
                                            <div class="relative">
                                                <input
                                                    class="w-full border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                                    placeholder="10" type="number" />
                                                <span
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#8a8060] font-bold">%</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="text-sm font-bold text-[#181611]">Giảm tối đa</label>
                                            <div class="relative">
                                                <input
                                                    class="w-full border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                                    placeholder="500.000" type="number" />
                                                <span
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#8a8060] font-bold">đ</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="flex flex-col gap-2">
                                            <label class="text-sm font-bold text-[#181611]">Đơn hàng tối thiểu</label>
                                            <div class="relative">
                                                <input
                                                    class="w-full border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                                    placeholder="1.000.000" type="number" />
                                                <span
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#8a8060] font-bold">đ</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="text-sm font-bold text-[#181611]">Tổng số mã phát
                                                hành</label>
                                            <input
                                                class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                                placeholder="100" type="number" />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <label class="text-sm font-bold text-[#181611]">Giới hạn mỗi khách</label>
                                            <input
                                                class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                                type="number" value="1" />
                                        </div>
                                    </div>
                                </section>
                                <hr class="border-[#e6e3db]" />
                                <!-- Timeline & Targeting -->
                                <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="flex flex-col gap-2">
                                        <label class="text-sm font-bold text-[#181611]">Ngày bắt đầu</label>
                                        <div class="relative">
                                            <span
                                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060] text-lg">calendar_today</span>
                                            <input
                                                class="w-full pl-10 border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                                type="date" />
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label class="text-sm font-bold text-[#181611]">Ngày kết thúc</label>
                                        <div class="relative">
                                            <span
                                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060] text-lg">event_busy</span>
                                            <input
                                                class="w-full pl-10 border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                                type="date" />
                                        </div>
                                    </div>
                                    <div class="md:col-span-2 flex flex-col gap-2">
                                        <label class="text-sm font-bold text-[#181611]">Đối tượng áp dụng</label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input checked="" class="text-primary focus:ring-primary"
                                                    name="target" type="radio" />
                                                <span class="text-sm">Toàn bộ cửa hàng</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input class="text-primary focus:ring-primary" name="target"
                                                    type="radio" />
                                                <span class="text-sm">Theo danh mục</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input class="text-primary focus:ring-primary" name="target"
                                                    type="radio" />
                                                <span class="text-sm">Theo sản phẩm</span>
                                            </label>
                                        </div>
                                    </div>
                                </section>
                                <div class="flex justify-end gap-3 pt-4">
                                    <button
                                        class="px-6 py-2 border border-[#e6e3db] rounded-lg text-sm font-bold hover:bg-gray-50 transition-colors"
                                        type="button">Hủy</button>
                                    <button
                                        class="px-8 py-2 bg-primary text-[#181611] rounded-lg text-sm font-bold hover:brightness-95 transition-all shadow-md"
                                        type="submit">Lưu chương trình</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Top Performing codes side info -->
                    <div class="flex flex-col gap-6">
                        <div class="bg-white dark:bg-gray-900 border border-[#e6e3db] rounded-xl p-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-[#8a8060] mb-4">Top 5 mã hiệu
                                quả</h3>
                            <div class="flex flex-col gap-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="size-6 bg-primary/20 text-primary flex items-center justify-center rounded-full text-xs font-bold">1</span>
                                    <div class="flex-1 flex flex-col">
                                        <span class="text-sm font-bold">BEEIPHONE15</span>
                                        <span class="text-[10px] text-[#8a8060]">84 lượt sử dụng</span>
                                    </div>
                                    <span class="text-sm font-bold text-green-600">8.4M</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="size-6 bg-gray-100 text-[#8a8060] flex items-center justify-center rounded-full text-xs font-bold">2</span>
                                    <div class="flex-1 flex flex-col">
                                        <span class="text-sm font-bold">HELLO2024</span>
                                        <span class="text-[10px] text-[#8a8060]">50 lượt sử dụng</span>
                                    </div>
                                    <span class="text-sm font-bold text-green-600">5.2M</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="size-6 bg-gray-100 text-[#8a8060] flex items-center justify-center rounded-full text-xs font-bold">3</span>
                                    <div class="flex-1 flex flex-col">
                                        <span class="text-sm font-bold">APPLEFAN</span>
                                        <span class="text-[10px] text-[#8a8060]">42 lượt sử dụng</span>
                                    </div>
                                    <span class="text-sm font-bold text-green-600">3.1M</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="size-6 bg-gray-100 text-[#8a8060] flex items-center justify-center rounded-full text-xs font-bold">4</span>
                                    <div class="flex-1 flex flex-col">
                                        <span class="text-sm font-bold">FREESHIP</span>
                                        <span class="text-[10px] text-[#8a8060]">12 lượt sử dụng</span>
                                    </div>
                                    <span class="text-sm font-bold text-green-600">1.2M</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="size-6 bg-gray-100 text-[#8a8060] flex items-center justify-center rounded-full text-xs font-bold">5</span>
                                    <div class="flex-1 flex flex-col">
                                        <span class="text-sm font-bold">WELCOMBEE</span>
                                        <span class="text-[10px] text-[#8a8060]">8 lượt sử dụng</span>
                                    </div>
                                    <span class="text-sm font-bold text-green-600">0.8M</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-primary/10 border border-primary/20 rounded-xl p-6 flex flex-col gap-3">
                            <span class="material-symbols-outlined text-primary">lightbulb</span>
                            <h4 class="text-sm font-bold">Mẹo tối ưu!</h4>
                            <p class="text-xs text-[#8a8060] leading-relaxed">Các chương trình giảm giá theo
                                <strong>phần trăm (%)</strong> thường có lượt sử dụng cao hơn 25% so với số tiền cố định
                                cho các sản phẩm phụ kiện.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

@endsection
