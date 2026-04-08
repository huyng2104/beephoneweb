@extends('admin.layouts.app')
@section('content')

@include('popup_notify.index')

{{-- ===== HEADER ===== --}}
<header class="dark:bg-gray-900  px-8 mt-5">
    <div class="flex flex-wrap justify-between items-end gap-4">
        <div class="flex flex-col gap-1">
            <h2 class="text-[#181611] dark:text-white text-3xl font-bold tracking-tight">Quản lý Đánh giá</h2>
            <p class="text-[#8a8060] text-sm">Duyệt, ẩn và phản hồi đánh giá từ khách hàng.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- @if($stats['pending'] > 0)
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-sm font-bold">
                <span class="material-symbols-outlined text-[18px]">pending</span>
                {{ $stats['pending'] }} chờ duyệt
            </span>
            @endif --}}
        </div>
    </div>
</header>

<div class="p-8 flex flex-col gap-8">

    {{-- ===== STATS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <a href="{{ route('admin.reviews.index') }}" class="flex flex-col gap-1 rounded-xl p-5 bg-white dark:bg-gray-900 border {{ request()->missing('status') ? 'border-primary ring-1 ring-primary' : 'border-[#e6e3db] hover:border-gray-400' }} transition-all shadow-sm hover:shadow-md cursor-pointer">
            <p class="text-[#8a8060] text-xs font-medium uppercase tracking-wide">Tổng đánh giá</p>
            <p class="text-[#181611] dark:text-white text-3xl font-bold">{{ number_format($stats['total']) }}</p>
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 0]) }}" class="flex flex-col gap-1 rounded-xl p-5 bg-amber-50 border {{ request('status') === '0' ? 'border-amber-500 ring-1 ring-amber-500' : 'border-amber-200 hover:border-amber-400' }} transition-all shadow-sm hover:shadow-md cursor-pointer">
            <p class="text-amber-600 text-xs font-medium uppercase tracking-wide">Chờ duyệt</p>
            <p class="text-amber-700 text-3xl font-bold">{{ number_format($stats['pending']) }}</p>
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 1]) }}" class="flex flex-col gap-1 rounded-xl p-5 bg-green-50 border {{ request('status') === '1' ? 'border-green-500 ring-1 ring-green-500' : 'border-green-200 hover:border-green-400' }} transition-all shadow-sm hover:shadow-md cursor-pointer">
            <p class="text-green-600 text-xs font-medium uppercase tracking-wide">Đang hiển thị</p>
            <p class="text-green-700 text-3xl font-bold">{{ number_format($stats['approved']) }}</p>
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 2]) }}" class="flex flex-col gap-1 rounded-xl p-5 bg-gray-50 border {{ request('status') === '2' ? 'border-gray-500 ring-1 ring-gray-500' : 'border-gray-200 hover:border-gray-400' }} transition-all shadow-sm hover:shadow-md cursor-pointer">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Đã ẩn</p>
            <p class="text-gray-700 text-3xl font-bold">{{ number_format($stats['hidden']) }}</p>
        </a>
        <div class="flex flex-col gap-2 rounded-xl p-5 bg-white dark:bg-gray-900 border border-[#e6e3db]">
            <p class="text-[#8a8060] text-xs font-medium uppercase tracking-wide">Điểm TB</p>
            <div class="flex items-end gap-1">
                <p class="text-[#181611] dark:text-white text-3xl font-bold">{{ $stats['average'] ?: '—' }}</p>
                @if($stats['average'])
                <span class="text-primary text-lg pb-0.5">★</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== RATING BREAKDOWN ===== --}}
    <div class="bg-white dark:bg-gray-900 border border-[#e6e3db] rounded-xl p-6">
        <h3 class="text-sm font-bold text-[#8a8060] uppercase tracking-wider mb-4">Phân bổ sao (đã duyệt)</h3>
        <div class="flex flex-col gap-2">
            @foreach($ratingBreakdown as $star => $count)
            @php $pct = $stats['approved'] > 0 ? round(($count / $stats['approved']) * 100) : 0; @endphp
            <div class="flex items-center gap-3 text-sm">
                <span class="w-6 font-bold text-[#181611] dark:text-white text-right">{{ $star }}</span>
                <span class="text-primary text-base leading-none">★</span>
                <div class="flex-1 h-2.5 bg-[#f5f3f0] dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                </div>
                <span class="w-8 text-right text-[#8a8060] font-medium">{{ $count }}</span>
                <span class="w-10 text-right text-[#8a8060] text-xs">({{ $pct }}%)</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="bg-white dark:bg-gray-900 border border-[#e6e3db] rounded-xl overflow-hidden flex flex-col">

        {{-- Filter bar --}}
        <div class="p-6 border-b border-[#e6e3db] flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.reviews.index') }}" method="GET" id="filter-form"
                  class="flex flex-wrap items-center gap-3 w-full">

                {{-- Search --}}
                <div class="relative flex-grow min-w-[200px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#8a8060] text-lg pointer-events-none">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Tìm sản phẩm, nội dung, tên KH..."
                           onchange="this.form.submit()"
                           class="w-full pl-10 pr-4 py-2 bg-[#f9f8f5] dark:bg-gray-800 border border-[#e6e3db] rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none transition-shadow">
                </div>

                {{-- Trạng thái --}}
                <select name="status" onchange="this.form.submit()"
                        class="px-3 py-2 border border-[#e6e3db] rounded-lg text-sm focus:ring-primary outline-none cursor-pointer bg-white dark:bg-gray-800">
                    <option value="">Tất cả trạng thái</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}> Chờ duyệt</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}> Hiển thị</option>
                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}> Đã ẩn</option>
                </select>

                {{-- Sao --}}
                <select name="rating" onchange="this.form.submit()"
                        class="px-3 py-2 border border-[#e6e3db] rounded-lg text-sm focus:ring-primary outline-none cursor-pointer bg-white dark:bg-gray-800">
                    <option value="">Tất cả sao</option>
                    @foreach(range(5, 1) as $star)
                    <option value="{{ $star }}" {{ request('rating') == $star ? 'selected' : '' }}>
                        {{ str_repeat('★', $star) }}{{ str_repeat('☆', 5 - $star) }} ({{ $star }} sao)
                    </option>
                    @endforeach
                </select>

                {{-- Đã mua hàng --}}
                {{-- <select name="is_purchased" onchange="this.form.submit()"
                        class="px-3 py-2 border border-[#e6e3db] rounded-lg text-sm focus:ring-primary outline-none cursor-pointer bg-white dark:bg-gray-800">
                    <option value="">Tất cả KH</option>
                    <option value="1" {{ request('is_purchased') === '1' ? 'selected' : '' }}>✔ Đã mua hàng</option>
                    <option value="0" {{ request('is_purchased') === '0' ? 'selected' : '' }}>? Chưa xác nhận</option>
                </select> --}}

                <select name="sort" onchange="this.form.submit()"
                        class="px-3 py-2 border border-[#e6e3db] rounded-lg text-sm focus:ring-primary outline-none cursor-pointer bg-white dark:bg-gray-800">
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}> Mới nhất</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}> Cũ nhất</option>
                    <option value="likes" {{ request('sort') === 'likes' ? 'selected' : '' }}> Nhiều Like nhất</option>
                </select>

                @if(request()->anyFilled(['search','status','rating','is_purchased']) || (request()->filled('sort') && request('sort') !== 'newest'))
                <a href="{{ route('admin.reviews.index') }}"
                   class="flex items-center gap-1 px-3 py-2 bg-gray-100 text-gray-600 border border-gray-200 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                    <span class="material-symbols-outlined text-sm">close</span> Xóa lọc
                </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f9f8f5] dark:bg-gray-800 text-[#8a8060] text-xs uppercase tracking-wider font-bold border-b border-[#e6e3db]">
                        <th class="px-6 py-4 w-[35%]">Sản phẩm & Khách hàng</th>
                        <th class="px-6 py-4 w-[30%]">Nội dung đánh giá</th>
                        <th class="px-6 py-4 text-center">Sao / Hữu ích</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e6e3db]">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-[#f9f8f5] dark:hover:bg-gray-800/50 transition-colors group" id="review-row-{{ $review->id }}">

                        {{-- Sản phẩm & Khách hàng --}}
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                @if($review->product?->thumbnail)
                                <img src="{{ asset('storage/' . $review->product->thumbnail) }}"
                                     alt="{{ $review->product->name }}"
                                     class="w-12 h-12 rounded-lg object-cover border border-[#e6e3db] flex-shrink-0">
                                @else
                                <div class="w-12 h-12 rounded-lg bg-[#f5f3f0] flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-[#8a8060]">smartphone</span>
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-bold text-[#181611] dark:text-white text-sm truncate max-w-[180px]">
                                        {{ $review->product?->name ?? '(Đã xóa)' }}
                                    </p>
                                    <p class="text-xs text-[#8a8060] mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">person</span>
                                        {{ $review->user?->name ?? 'Khách ẩn danh' }}
                                    </p>
                                    {{-- @if($review->is_purchased)
                                    <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold rounded-full">
                                        <span class="material-symbols-outlined text-[12px]">verified</span>
                                        Đã mua hàng
                                    </span>
                                    @endif --}}
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $review->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Nội dung --}}
                        <td class="px-6 py-4">
                            <p class="text-sm text-[#181611] dark:text-gray-200 line-clamp-3 leading-relaxed">
                                {{ $review->comment }}
                            </p>

                            {{-- Ảnh đính kèm --}}
                            @if($review->images->isNotEmpty())
                            <div class="flex gap-1.5 mt-2 flex-wrap">
                                @foreach($review->images->take(4) as $img)
                                <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                         class="w-10 h-10 rounded-lg object-cover border border-[#e6e3db] hover:opacity-80 transition-opacity">
                                </a>
                                @endforeach
                                @if($review->images->count() > 4)
                                <div class="w-10 h-10 rounded-lg bg-[#f5f3f0] flex items-center justify-center text-xs font-bold text-[#8a8060] border border-[#e6e3db]">
                                    +{{ $review->images->count() - 4 }}
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- Phản hồi của admin --}}
                            @if($review->hasReply())
                            <div class="mt-2 p-2.5 bg-blue-50 border-l-2 border-blue-400 rounded text-xs text-blue-700">
                                <span class="font-bold">Phản hồi:</span> {{ Str::limit($review->reply_comment, 80) }}
                            </div>
                            @endif
                        </td>

                        {{-- Sao & Hữu ích --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-1.5">
                                <div class="flex text-primary text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-[16px] {{ $i <= $review->rating ? 'text-primary' : 'text-gray-300' }}">star</span>
                                    @endfor
                                </div>
                                <span class="text-xs text-[#8a8060] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">thumb_up</span>
                                    {{ $review->helpful_count }}
                                </span>
                            </div>
                        </td>

                        {{-- Trạng thái --}}
                        <td class=" text-center">
                            @php
                                $statusClass = match($review->status) {
                                    0 => 'bg-amber-100 text-amber-700 border-amber-200',
                                    1 => 'bg-green-100 text-green-700 border-green-200',
                                    2 => 'bg-gray-100 text-gray-600 border-gray-200',
                                };
                                $statusLabel = match($review->status) {
                                    0 => 'Chờ duyệt',
                                    1 => 'Hiển thị',
                                    2 => 'Đã ẩn',
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        {{-- Hành động --}}
                        <td class="px-6 py-4">
                            <div class="flex justify-end items-center gap-1">

                                {{-- Duyệt --}}
                                @if($review->status !== 1)
                                <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Duyệt"
                                            class="p-2 hover:bg-green-50 rounded-lg text-[#8a8060] hover:text-green-600 transition-colors">
                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                    </button>
                                </form>
                                @endif

                                {{-- Ẩn --}}
                                @if($review->status !== 2)
                                <form action="{{ route('admin.reviews.hide', $review->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Ẩn"
                                            class="p-2 hover:bg-orange-50 rounded-lg text-[#8a8060] hover:text-orange-500 transition-colors">
                                        <span class="material-symbols-outlined text-lg">visibility_off</span>
                                    </button>
                                </form>
                                @endif

                                {{-- Phản hồi --}}
                                <button type="button" title="Phản hồi"
                                        onclick="openReplyModal({{ $review->id }}, `{{ addslashes($review->reply_comment ?? '') }}`)"
                                        class="p-2 hover:bg-blue-50 rounded-lg text-[#8a8060] hover:text-blue-500 transition-colors">
                                    <span class="material-symbols-outlined text-lg">reply</span>
                                </button>

                                {{-- Xóa --}}
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Xóa đánh giá này? Hành động không thể hoàn tác.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Xóa"
                                            class="p-2 hover:bg-red-50 rounded-lg text-[#8a8060] hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-[#8a8060]">
                                <span class="material-symbols-outlined text-5xl text-gray-300">rate_review</span>
                                <p class="font-medium">Chưa có đánh giá nào.</p>
                                @if(request()->anyFilled(['search','status','rating','is_purchased']))
                                <a href="{{ route('admin.reviews.index') }}"
                                   class="text-sm text-primary hover:underline">Xóa bộ lọc để xem tất cả</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reviews->hasPages())
        <div class="p-4 border-t border-[#e6e3db] flex justify-between items-center bg-gray-50/50 flex-wrap gap-3">
            <p class="text-sm text-[#8a8060]">
                Hiển thị {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} / {{ $reviews->total() }} đánh giá
            </p>
            <div class="flex gap-1">
                @if($reviews->onFirstPage())
                <button class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] opacity-40" disabled>
                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                </button>
                @else
                <a href="{{ $reviews->previousPageUrl() }}"
                   class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611] transition-all">
                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                </a>
                @endif

                @foreach($reviews->getUrlRange(max(1, $reviews->currentPage()-2), min($reviews->lastPage(), $reviews->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="size-8 flex items-center justify-center rounded border font-bold text-xs transition-all
                          {{ $page == $reviews->currentPage() ? 'border-primary bg-primary text-[#181611]' : 'border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611]' }}">
                    {{ $page }}
                </a>
                @endforeach

                @if($reviews->hasMorePages())
                <a href="{{ $reviews->nextPageUrl() }}"
                   class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] hover:bg-primary hover:text-[#181611] transition-all">
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </a>
                @else
                <button class="size-8 flex items-center justify-center rounded border border-[#e6e3db] bg-white text-[#8a8060] opacity-40" disabled>
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </button>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- end table card --}}
</div>

{{-- ===== REPLY MODAL ===== --}}
<div id="reply-modal"
     class="fixed inset-0 z-[9999] flex items-center justify-center px-4 hidden"
     aria-hidden="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeReplyModal()"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-[#e6e3db] overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#e6e3db]">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">reply</span>
                <h3 class="text-base font-bold text-[#181611] dark:text-white">Phản hồi đánh giá</h3>
            </div>
            <button onclick="closeReplyModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-[#8a8060]">close</span>
            </button>
        </div>

        {{-- Body --}}
        <form id="reply-form" method="POST" action="">
            @csrf
            <div class="px-6 py-5 flex flex-col gap-4">
                <label for="reply_comment" class="block text-sm font-bold text-[#181611] dark:text-white">
                    Nội dung phản hồi
                </label>
                <textarea id="reply_comment" name="reply_comment" rows="5" required
                          class="w-full border border-[#e6e3db] rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary outline-none transition-shadow resize-none dark:bg-gray-800 dark:text-white"
                          placeholder="Cảm ơn bạn đã đánh giá sản phẩm..."></textarea>
            </div>
            <div class="px-6 py-4 border-t border-[#e6e3db] flex justify-end gap-3 bg-gray-50/50">
                <button type="button" onclick="closeReplyModal()"
                        class="px-5 py-2.5 border border-[#e6e3db] rounded-xl text-sm font-bold text-[#8a8060] hover:bg-gray-100 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                        class="px-5 py-2.5 bg-primary text-[#181611] rounded-xl text-sm font-bold hover:brightness-105 transition-all shadow-sm">
                    Gửi phản hồi
                </button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
    let currentReviewId = null;

    function openReplyModal(reviewId, existingReply) {
        currentReviewId = reviewId;

        // Cập nhật action URL
        const form = document.getElementById('reply-form');
        form.action = `/admin/reviews/${reviewId}/reply`;

        // Điền nội dung cũ nếu có
        document.getElementById('reply_comment').value = existingReply || '';

        const modal = document.getElementById('reply-modal');
        modal.classList.remove('hidden');
        modal.removeAttribute('aria-hidden');
        document.getElementById('reply_comment').focus();
    }

    function closeReplyModal() {
        const modal = document.getElementById('reply-modal');
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        currentReviewId = null;
    }

    // Đóng modal bằng ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeReplyModal();
    });
</script>
@endpush

@endsection
