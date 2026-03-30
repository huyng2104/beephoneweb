<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $product->name }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ffc105',
                        'background-light': '#f8f8f5',
                        'background-dark': '#231e0f',
                    },
                    fontFamily: {
                        display: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="/css/comments.css">
</head>
<body class="bg-[#14160f] font-display text-slate-100">
    <div class="min-h-screen bg-[#14160f] py-8 sm:py-10">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#141a1e] shadow-[0_16px_45px_-30px_rgba(0,0,0,0.75)]">
                <div class="border-b border-white/10 px-6 py-5 sm:px-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex flex-1 items-start gap-5">
                            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-white/10 bg-black/20 sm:h-32 sm:w-32">
                                @if($product->thumbnail)
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-5xl text-slate-500">image</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full bg-primary/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-100">San pham</span>
                                    @if($product->status === 'active')
                                        <span class="inline-flex items-center rounded-full bg-green-500/15 px-3 py-1 text-xs font-semibold text-green-200">Hien thi</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">{{ $product->status ?? 'Khong ro' }}</span>
                                    @endif
                                </div>
                                <h1 class="text-3xl font-black leading-tight tracking-tight text-white sm:text-4xl">{{ $product->name }}</h1>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-bold text-slate-200 shadow-sm transition hover:border-primary hover:text-primary">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Quay lai danh sach san pham
                            </a>
                            <a href="#comments" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-slate-900 shadow-sm transition hover:brightness-105">
                                <span class="material-symbols-outlined text-[18px]">forum</span>
                                Den phan comment
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 border-b border-white/10 bg-black/10 px-6 py-5 text-sm text-slate-300 sm:grid-cols-3 sm:px-8">
                    <div class="rounded-xl border border-white/10 bg-black/10 px-4 py-3 shadow-sm">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">So comment</div>
                        <div class="mt-1 text-2xl font-black text-white">{{ $comments->count() }}</div>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-black/10 px-4 py-3 shadow-sm">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Danh gia</div>
                        <div class="mt-1 text-2xl font-black text-white">{{ number_format($comments->whereNotNull('rating')->avg('rating') ?? 0, 1) }}</div>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-black/10 px-4 py-3 shadow-sm">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Tra loi</div>
                        <div class="mt-1 text-2xl font-black text-white">{{ $comments->sum(fn($comment) => $comment->children->count()) }}</div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-[#0f141a] text-white shadow-[0_15px_45px_-25px_rgba(15,20,26,0.85)]">
                <div class="grid gap-8 px-6 py-6 lg:grid-cols-[260px_minmax(0,1fr)_1px]">
                    <div class="flex flex-col justify-center">
                        <div class="flex items-end gap-2">
                            <span class="text-6xl font-black leading-none">{{ number_format($averageRating, 1) }}</span>
                            <span class="pb-2 text-3xl font-bold text-slate-300">/5</span>
                        </div>
                         <div class="mt-4 flex items-center gap-0.5 text-primary">
                             @for($i = 1; $i <= 5; $i++)
                                 <span class="material-symbols-outlined text-[22px]">star</span>
                             @endfor
                         </div>
                         <p class="mt-2 text-lg font-medium text-slate-200">{{ $totalRatings }} luot danh gia</p>
                        <button id="open-review-modal" type="button" class="mt-5 inline-flex h-12 w-44 items-center justify-center rounded-xl bg-primary px-6 text-base font-black text-slate-900 shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-primary/60">
                            Viết đánh giá
                        </button>
                    </div>

                    <div class="flex flex-col justify-center gap-3 lg:pr-6">
                        @foreach($ratingBreakdown as $star => $count)
                            @php
                                $percent = $totalRatings > 0 ? round(($count / $totalRatings) * 100, 2) : 0;
                            @endphp
                            <div class="grid grid-cols-[30px_minmax(0,1fr)_84px] items-center gap-3">
                                <div class="flex items-center gap-1 text-base font-bold text-white">
                                    <span>{{ $star }}</span>
                                    <span class="material-symbols-outlined text-primary text-[18px]">star</span>
                                </div>
                                 <div class="h-2.5 overflow-hidden rounded-full bg-slate-800">
                                     <div class="h-full rounded-full bg-primary transition-all duration-300" style="width: {{ $percent }}%"></div>
                                 </div>
                                 <div class="text-right text-sm text-slate-300">{{ $count }} danh gia</div>
                             </div>
                         @endforeach
                     </div>

                     <div class="hidden lg:block w-px bg-white/10"></div>
                 </div>
             </div>

            <div id="review-modal" aria-hidden="true" class="fixed inset-0 z-[9998] flex items-start justify-center px-4 py-8 opacity-0 pointer-events-none transition duration-200 ease-out sm:py-12">
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-review-close></div>

                <div id="review-modal-panel" class="relative w-full max-w-5xl overflow-hidden rounded-2xl border border-white/10 bg-[#141a1e] shadow-[0_20px_60px_-40px_rgba(0,0,0,0.9)] opacity-0 translate-y-3 scale-95 transition duration-200 ease-out">
                    <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
                        <h3 class="text-lg font-black text-white">Danh gia & nhan xet</h3>
                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-200 transition hover:border-white/20 hover:bg-white/10" data-review-close>
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="grid gap-6 px-6 py-6 lg:grid-cols-[260px_minmax(0,1fr)]">
                        <div class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-black/10 p-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-white/10 bg-black/20">
                                    @if($product->thumbnail)
                                        <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-4xl text-slate-500">image</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold uppercase tracking-wide text-slate-400">San pham</div>
                                    <div class="mt-1 line-clamp-2 text-base font-black text-white">{{ $product->name }}</div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-[#0f141a] p-4">
                                <div class="text-sm font-bold text-slate-200">Danh gia chung</div>
                                <div class="mt-3 flex items-center gap-2 text-primary" data-rating-stars>
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" class="review-star inline-flex items-center justify-center rounded-lg p-1 transition hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-primary/40" data-value="{{ $i }}" aria-label="{{ $i }} sao">
                                            <span class="material-symbols-outlined text-[26px]">star</span>
                                        </button>
                                    @endfor
                                </div>
                                <p class="mt-2 text-xs text-slate-400" data-rating-label>Chon so sao de danh gia</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-black/10 p-5">
                            <form action="{{ route('products.comments.store', $product) }}" method="POST" enctype="multipart/form-data" id="review-form" class="space-y-4">
                                @csrf
                                <input type="hidden" name="rating" id="review_rating" value="">

                                @guest
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="review_guest_name" class="block text-xs font-bold text-slate-300">Ten cua ban</label>
                                        <input id="review_guest_name" type="text" name="guest_name" required class="mt-2 w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-primary focus:ring-primary/30" placeholder="Nhap ten hien thi">
                                    </div>
                                    <div>
                                        <label for="review_guest_email" class="block text-xs font-bold text-slate-300">Email</label>
                                        <input id="review_guest_email" type="email" name="guest_email" required class="mt-2 w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-primary focus:ring-primary/30" placeholder="email@example.com">
                                    </div>
                                </div>
                                @endguest

                                <div>
                                    <label for="review_content" class="block text-xs font-bold text-slate-300">Nhan xet</label>
                                    <textarea id="review_content" name="content" rows="6" required class="mt-2 w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-primary focus:ring-primary/30" placeholder="Xin moi chia se mot so cam nhan ve san pham (nhap toi thieu 15 ki tu)"></textarea>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="review_image" class="block text-xs font-bold text-slate-300">Anh (tuy chon)</label>
                                        <input id="review_image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-2 w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-sm text-slate-200 file:mr-4 file:rounded-lg file:border-0 file:bg-white/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-slate-100 hover:file:bg-white/15">
                                    </div>
                                    <div class="flex items-end">
                                        <div class="w-full rounded-xl border border-white/10 bg-[#0f141a] px-4 py-3 text-xs text-slate-400">
                                            Meo: ban co the nhan <span class="font-bold text-slate-200">ESC</span> de dong form.
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-primary px-6 py-4 text-sm font-black text-slate-900 shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-primary/60">
                                    Gui danh gia
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
 
            <div id="comments" class="space-y-6">
                <div class="min-w-0 overflow-hidden rounded-2xl border border-white/10 bg-[#141a1e] shadow-[0_16px_45px_-30px_rgba(0,0,0,0.75)]">
                    <div class="border-b border-white/10 px-6 py-4 sm:px-8">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="flex items-center gap-2 text-xl font-black text-white">
                                <span class="material-symbols-outlined text-primary">chat</span>
                                Danh sach comment
                            </h2>
                            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-slate-200">
                                {{ $comments->count() }} comment goc
                            </span>
                        </div>
                    </div>
                    <div class="comments-list px-6 py-2 sm:px-8">
                        @forelse($comments as $comment)
                            @include('components.comment', ['comment' => $comment, 'product' => $product])
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center text-sm font-medium text-slate-500">
                                Chua co comment nao. Ban co the tao comment dau tien ngay bay gio.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var openBtn = document.getElementById('open-review-modal');
            var modal = document.getElementById('review-modal');
            var panel = document.getElementById('review-modal-panel');
            var ratingInput = document.getElementById('review_rating');
            var ratingLabel = modal ? modal.querySelector('[data-rating-label]') : null;
            var starsWrap = modal ? modal.querySelector('[data-rating-stars]') : null;

            if (!openBtn || !modal || !panel) return;

            function setRating(value) {
                if (!ratingInput) return;
                ratingInput.value = String(value || '');

                if (starsWrap) {
                    var stars = starsWrap.querySelectorAll('.review-star');
                    stars.forEach(function (btn) {
                        var v = Number(btn.getAttribute('data-value') || 0);
                        btn.classList.toggle('text-primary', v <= value);
                        btn.classList.toggle('text-slate-600', v > value);
                    });
                }

                if (ratingLabel) {
                    var map = { 1: 'Rat te', 2: 'Te', 3: 'Binh thuong', 4: 'Tot', 5: 'Tuyet voi' };
                    ratingLabel.textContent = value ? value + '/5 - ' + (map[value] || '') : 'Chon so sao de danh gia';
                }
            }

            function openModal() {
                modal.setAttribute('aria-hidden', 'false');
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');

                panel.classList.remove('opacity-0', 'translate-y-3', 'scale-95');
                panel.classList.add('opacity-100', 'translate-y-0', 'scale-100');

                if (ratingInput && !ratingInput.value) setRating(5);

                var content = document.getElementById('review_content');
                content && content.focus && content.focus();
            }

            function closeModal() {
                modal.setAttribute('aria-hidden', 'true');
                modal.classList.remove('opacity-100', 'pointer-events-auto');
                modal.classList.add('opacity-0', 'pointer-events-none');

                panel.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
                panel.classList.add('opacity-0', 'translate-y-3', 'scale-95');
            }

            openBtn.addEventListener('click', openModal);

            modal.addEventListener('click', function (e) {
                var target = e.target;
                if (!(target instanceof Element)) return;

                if (target.closest('[data-review-close]')) {
                    closeModal();
                    return;
                }

                var starBtn = target.closest('.review-star');
                if (starBtn) {
                    var v = Number(starBtn.getAttribute('data-value') || 0);
                    if (v >= 1 && v <= 5) setRating(v);
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape') return;
                if (modal.getAttribute('aria-hidden') === 'true') return;
                closeModal();
            });

            if (starsWrap) {
                starsWrap.querySelectorAll('.review-star').forEach(function (btn) {
                    btn.classList.add('text-slate-600');
                });
            }
        })();
    </script>
</body>
</html>
