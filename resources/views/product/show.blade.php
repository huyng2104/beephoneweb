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
<body class="bg-background-light font-display text-slate-900">
    <div class="min-h-screen bg-background-light py-8 sm:py-10">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex flex-1 items-start gap-5">
                            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 sm:h-32 sm:w-32">
                                @if($product->thumbnail)
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-5xl text-slate-300">image</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full bg-primary/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-slate-900">San pham</span>
                                    @if($product->status === 'active')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Hien thi</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $product->status ?? 'Khong ro' }}</span>
                                    @endif
                                </div>
                                <h1 class="text-3xl font-black leading-tight tracking-tight text-slate-900 sm:text-4xl">{{ $product->name }}</h1>
                                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                                    {{ $product->description ?: 'Chua co mo ta cho san pham nay.' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:border-primary hover:text-primary">
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

                <div class="grid gap-4 border-b border-slate-100 bg-slate-50/80 px-6 py-5 text-sm text-slate-600 sm:grid-cols-3 sm:px-8">
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">So comment</div>
                        <div class="mt-1 text-2xl font-black text-slate-900">{{ $comments->count() }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Danh gia</div>
                        <div class="mt-1 text-2xl font-black text-slate-900">{{ number_format($comments->whereNotNull('rating')->avg('rating') ?? 0, 1) }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Tra loi</div>
                        <div class="mt-1 text-2xl font-black text-slate-900">{{ $comments->sum(fn($comment) => $comment->children->count()) }}</div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-[#0f141a] text-white shadow-[0_15px_45px_-25px_rgba(15,20,26,0.85)]">
                <div class="grid gap-6 px-5 py-5 lg:grid-cols-[220px_minmax(0,1fr)] lg:px-6">
                    <div class="flex flex-col justify-center">
                        <div class="flex items-end gap-2">
                            <span class="text-5xl font-black leading-none">{{ number_format($averageRating, 1) }}</span>
                            <span class="pb-1 text-2xl font-bold text-slate-300">/5</span>
                        </div>
                        <div class="mt-3 flex items-center gap-0.5 text-primary">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-[22px]">star</span>
                            @endfor
                        </div>
                        <p class="mt-2 text-lg font-medium text-slate-200">{{ $totalRatings }} luot danh gia</p>
                    </div>

                    <div class="flex flex-col justify-center gap-3">
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
                                    <div class="h-full rounded-full bg-red-500 transition-all duration-300" style="width: {{ $percent }}%"></div>
                                </div>
                                <div class="text-right text-sm text-slate-300">{{ $count }} danh gia</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="comments" class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
                <div class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-6 py-4 sm:px-8">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="flex items-center gap-2 text-xl font-black text-slate-900">
                                <span class="material-symbols-outlined text-primary">chat</span>
                                Danh sach comment
                            </h2>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
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

                <div class="h-fit self-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:sticky lg:top-6">
                    <div class="border-b border-slate-100 px-6 py-4">
                        <h2 class="flex items-center gap-2 text-xl font-black text-slate-900">
                            <span class="material-symbols-outlined text-primary">rate_review</span>
                            Viet comment
                        </h2>
                    </div>
                    <div class="px-5 py-5 sm:px-6">
                        <form action="{{ route('products.comments.store', $product) }}" method="POST" enctype="multipart/form-data" class="comment-form">
                            @csrf
                            @guest
                            <div>
                                <label for="guest_name" class="comment-label">Ten cua ban</label>
                                <input id="guest_name" type="text" name="guest_name" required class="comment-input" placeholder="Nhap ten hien thi">
                            </div>
                            <div>
                                <label for="guest_email" class="comment-label">Email</label>
                                <input id="guest_email" type="email" name="guest_email" class="comment-input" placeholder="email@example.com">
                            </div>
                            @endguest
                            <div>
                                <label for="rating" class="comment-label">Danh gia</label>
                                <select id="rating" name="rating" class="comment-input">
                                    <option value="">Khong danh gia</option>
                                    @for ($i=1;$i<=5;$i++)
                                        <option value="{{ $i }}">{{ $i }} sao</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label for="content" class="comment-label">Noi dung</label>
                                <textarea id="content" name="content" rows="5" required class="comment-input comment-textarea" placeholder="Nhap noi dung comment cua ban..."></textarea>
                            </div>
                            <div>
                                <label for="image" class="comment-label">Anh comment</label>
                                <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="comment-file-input">
                            </div>
                            <button type="submit" class="comment-submit">
                                Dang comment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
