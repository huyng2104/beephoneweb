@extends('admin.layouts.app')
@section('content')
    <div class="p-8 flex flex-col gap-8">
        <!-- Create/Edit Promotion Section -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 pb-20">
            <div class="xl:col-span-2 flex flex-col gap-6">
                <div class="bg-white dark:bg-gray-900 border border-[#e6e3db] rounded-xl p-8">
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">add_circle</span>
                        Chi tiết mã giảm giá mới
                    </h3>
                    <form method="post" action="{{ route('admin.vouchers.store') }}" class="space-y-8">
                        @csrf
                        <!-- Basic Info -->
                        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-[#181611]">Tên chương trình *</label>
                                <input class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                    placeholder="Ví dụ: Sale hè rực rỡ" type="text" value="{{ old('name') }}"
                                    name="name" />
                                @error('name')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-[#181611]">Mã Code *</label>
                                <div class="flex gap-2">
                                    <input id="voucherCode"
                                        class="flex-1 border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary  "
                                        placeholder="Ví dụ: SUMMER24" type="text" value="{{ old('code') }}"
                                        name="code" />

                                    <button id="generateCode"
                                        class="px-3 bg-gray-100 rounded-lg text-xs  text-gray-500 hover:bg-gray-200 transition-colors "
                                        type="button">
                                        Ngẫu nhiên
                                    </button>

                                </div>
                                @error('code')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="md:col-span-2 flex flex-col gap-2">
                                <label class="text-sm font-bold text-[#181611]">Mô tả</label>
                                <textarea name="description" class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                    placeholder="Nội dung hiển thị cho khách hàng..." rows="2">{{ old('description') }}</textarea>
                            </div>
                        </section>
                        <hr class="border-[#e6e3db]" />
                        <!-- Conditions -->
                        <section class="space-y-6">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-[#8a8060]">Điều kiện
                                &amp; Giá trị</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-bold  text-[#181611]">Loại giảm giá</label>
                                    <select name="discount_type"
                                        class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary">
                                        <option value="">-- Loại --</option>
                                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>
                                            Giảm theo %
                                        </option>
                                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>
                                            Giảm theo tiền
                                        </option>
                                    </select>
                                    @error('discount_type')
                                        <span class="text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-bold text-[#181611]">Giá trị giảm</label>
                                    <div class="relative">
                                        <input name="discount_value"
                                            class="w-full border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                            placeholder="10" value="{{ old('discount_value') }}" type="number" />
                                    </div>
                                    @error('discount_value')
                                        <span class="text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-bold text-[#181611]">Giảm tối đa</label>
                                    <div class="relative">
                                        <input name="max_discount"
                                            class="w-full border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                            placeholder="500.000" value="{{ old('max_discount') }}" type="number" />
                                        <span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#8a8060] font-bold">đ</span>
                                    </div>
                                    @error('max_discount')
                                        <span class="text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-bold text-[#181611]">Đơn hàng tối thiểu</label>
                                    <div class="relative">
                                        <input name="min_order_value"
                                            class="w-full border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                            placeholder="1.000.000" value="{{ old('min_order_value') }}" type="number" />
                                        <span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[#8a8060] font-bold">đ</span>
                                    </div>
                                    @error('min_order_value')
                                        <span class="text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-bold text-[#181611]">Tổng số mã phát
                                        hành</label>
                                    <input class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                        placeholder="100" value="{{ old('usage_limit') }}" type="number"
                                        name="usage_limit" />
                                    @error('usage_limit')
                                        <span class="text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-sm font-bold text-[#181611]">Giới hạn mỗi khách</label>
                                    <input class="border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                        type="number" value="{{ old('usage_limit_per_user') }}"
                                        name="usage_limit_per_user" />
                                    @error('usage_limit_per_user')
                                        <span class="text-red-600">{{ $message }}</span>
                                    @enderror
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
                                    <input name="start_date"
                                        class="w-full pl-10 border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                        type="date" value="{{ old('start_date') }}" />
                                </div>
                                @error('start_date')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-[#181611]">Ngày kết thúc</label>
                                <div class="relative">
                                    <span
                                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060] text-lg">event_busy</span>
                                    <input name="end_date"
                                        class="w-full pl-10 border-[#e6e3db] rounded-lg focus:ring-primary focus:border-primary"
                                        type="date" value="{{ old('end_date') }}" />
                                </div>
                                @error('end_date')
                                    <span class="text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="allStore" name="status" checked value="1"
                                    class="accent-black">
                                <span class="font-semibold">Trạng thái hoạt động</span>
                            </label>
                            <div class="md:col-span-2 space-y-4">

                                <label class="text-sm font-bold text-[#181611]">
                                    Đối tượng áp dụng
                                </label>

                                <!-- ================= DANH MỤC ================= -->
                                <div class="border rounded-xl overflow-hidden">
                                    <button type="button" onclick="toggleBox('categoryBox')"
                                        class="w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 font-semibold">
                                        Theo danh mục
                                        <span>+</span>
                                    </button>

                                    <div id="categoryBox" class="{{ old('categories') ? '' : 'hidden' }}  p-4 bg-white border-t">
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">

                                            @if (!$categories->isEmpty())
                                                @foreach ($categories as $category)
                                                    <label
                                                        class="flex items-center gap-2 p-2 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                                        <input type="checkbox" name="categories[]"
                                                            value="{{ $category->id }}" class="accent-black"
                                                            {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>

                                                        {{ $category->name }}
                                                    </label>
                                                @endforeach
                                            @else
                                                <span class="text-red-500">Chưa có danh mục!</span>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <!-- ================= SẢN PHẨM ================= -->
                                <div class="border rounded-xl overflow-hidden">
                                    <button type="button" onclick="toggleBox('productBox')"
                                        class="w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 font-semibold">
                                        Theo sản phẩm
                                        <span>+</span>
                                    </button>

                                    <div id="productBox" class="{{ old('products') ? '' : 'hidden' }}  p-4 bg-white border-t">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            @if ($products->isEmpty())
                                                <span class="text-red-500">Chưa có sản phẩm!</span>
                                            @else
                                                @foreach ($products as $product)
                                                    <label
                                                        class="flex items-center gap-2 p-2 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                                        <input type="checkbox" name="products[]"
                                                            value="{{ $product->id }}"
                                                            {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}
                                                            class="accent-black">

                                                        {{ $product->name }}
                                                    </label>
                                                @endforeach
                                            @endif



                                        </div>
                                    </div>
                                </div>

                                <!-- ================= THƯƠNG HIỆU ================= -->
                                <div class="border rounded-xl overflow-hidden">
                                    <button type="button" onclick="toggleBox('brandBox')"
                                        class="w-full flex justify-between items-center p-3 bg-gray-50 hover:bg-gray-100 font-semibold">
                                        Theo thương hiệu
                                        <span>+</span>
                                    </button>

                                    <div id="brandBox" class="{{ old('brands') ? '' : 'hidden' }}  p-4 bg-white border-t">
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">


                                            @if (!$brands->isEmpty())
                                                @foreach ($brands as $brand)
                                                    <label
                                                        class="flex items-center gap-2 p-2 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                                        <input type="checkbox" name="brands[]"
                                                            value="{{ $brand->id }}"
                                                            {{ in_array($brand->id, old('brands', [])) ? 'checked' : '' }}
                                                            class="accent-black">
                                                        {{ $brand->name }}
                                                    </label>
                                                @endforeach
                                            @else
                                                <span class="text-red-500">Chưa có thương hiệu!</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <script>
                                function toggleBox(id) {
                                    document.getElementById(id).classList.toggle("hidden");
                                }
                            </script>
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
        <script>
            document.getElementById('generateCode').addEventListener('click', function() {

                const length = 15;
                const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

                let result = "";

                for (let i = 0; i < length; i++) {
                    result += characters.charAt(Math.floor(Math.random() * characters.length));
                }

                document.getElementById('voucherCode').value = result;
            });
        </script>
    </div>
@endsection
