@php
    $hasAnyError = isset($errors) && $errors->any();
@endphp

@if (session('success') || session('error') || $hasAnyError)
    <div class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] w-[min(92vw,520px)] space-y-2">
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 shadow-lg">
                <div class="font-bold">Thành công</div>
                <div class="text-sm">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error') || $hasAnyError)
            <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 shadow-lg">
                <div class="font-bold">Lỗi</div>
                <div class="text-sm">
                    @if (session('error'))
                        {{ session('error') }}
                    @else
                        {{ $errors->first() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
@endif

