@extends('admin.layouts.app')

@section('content')
<div class="bg-background-light text-gray-800 min-h-screen antialiased font-sans">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">chat</span>
                    Quan ly Comments
                </h1>
            </div>
            <div class="text-sm text-slate-500 font-semibold">
                Tong so: {{ $comments->count() }} comment
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-red-600 text-lg">error</span>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Loai</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">User</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Anh</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Noi dung</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Thoi gian</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($comments as $comment)
                            <tr class="hover:bg-yellow-50/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-slate-700">#{{ $comment->id }}</td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @if($comment->parent_id)
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex w-fit items-center rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-bold text-blue-700">
                                                Reply
                                            </span>
                                            <span class="text-xs text-slate-400">
                                                Thuoc comment #{{ $comment->parent_id }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-[11px] font-bold text-green-700">
                                            Comment mở đầu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 font-medium">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $comment->user?->name ?? $comment->guest_name ?? 'Guest' }}</span>
                                        @if($comment->user && in_array($comment->user->role?->name, ['admin', 'staff'], true))
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-[11px] font-black text-yellow-800 border border-yellow-200">
                                                Admin
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($comment->product)
                                        <a href="{{ route('products.show', $comment->product->id) }}#comments" class="font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                            {{ $comment->product->name }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">San pham da xoa</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($comment->image_path)
                                        <img src="{{ asset('storage/' . $comment->image_path) }}" alt="comment image" class="h-14 w-14 rounded-lg border border-slate-200 object-cover bg-slate-50">
                                    @else
                                        <div class="flex h-14 w-14 items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50 text-slate-300">
                                            <span class="material-symbols-outlined text-[20px]">image</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 max-w-xl">
                                    <div class="{{ $comment->parent_id ? 'border-l-4 border-blue-200 pl-4' : '' }}">
                                        <div class="line-clamp-2">{{ $comment->content }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 whitespace-nowrap">
                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($comment->product)
                                        <a href="{{ route('products.show', $comment->product->id) }}#comment-{{ $comment->id }}" class="inline-flex items-center justify-center rounded-lg bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors border border-slate-200">
                                            Xem lai
                                        </a>
                                    @else
                                        <span class="inline-flex items-center justify-center rounded-lg bg-slate-50 px-3 py-2 text-xs font-bold text-slate-400 border border-slate-200 cursor-not-allowed">
                                            Xem lai
                                        </span>
                                    @endif
                                    <button type="button"
                                        class="inline-flex items-center justify-center rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100 transition-colors border border-blue-200 ml-2"
                                        data-reply-toggle="{{ $comment->id }}">
                                        Reply
                                    </button>
                                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline ml-2" data-confirm-delete-comment>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary px-3 py-2 text-xs font-bold text-slate-900 hover:brightness-105 transition-colors border border-primary/40">
                                            Xoa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="reply-row-{{ $comment->id }}" class="hidden bg-slate-50/60">
                                <td colspan="8" class="px-6 py-4">
                                    <form action="{{ route('admin.comments.reply', $comment) }}" method="POST" class="flex flex-col gap-3">
                                        @csrf
                                        <textarea name="content" rows="3" required
                                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-primary focus:ring-primary/20"
                                            placeholder="Nhập nội dung trả lời..."></textarea>
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100"
                                                data-reply-cancel="{{ $comment->id }}">
                                                Huy
                                            </button>
                                            <button type="submit"
                                                class="rounded-lg bg-primary px-3 py-2 text-xs font-bold text-slate-900 hover:brightness-105 border border-primary/40">
                                                Gửi
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-gray-400 bg-gray-50/50">
                                    <span class="material-symbols-outlined text-5xl mb-3 text-gray-300 block">chat</span>
                                    <p class="text-sm font-medium text-gray-500">Chua co comment nao.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('[data-reply-toggle]');
        const cancelButtons = document.querySelectorAll('[data-reply-cancel]');

        function hideAll() {
            document.querySelectorAll('[id^=\"reply-row-\"]').forEach((row) => row.classList.add('hidden'));
        }

        toggleButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-reply-toggle');
                const row = document.getElementById(`reply-row-${id}`);
                if (!row) return;

                const willShow = row.classList.contains('hidden');
                hideAll();
                if (willShow) {
                    row.classList.remove('hidden');
                    const textarea = row.querySelector('textarea[name=\"content\"]');
                    if (textarea) textarea.focus();
                }
            });
        });

        cancelButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-reply-cancel');
                const row = document.getElementById(`reply-row-${id}`);
                if (row) row.classList.add('hidden');
            });
        });
    });
</script>

@include('partials.confirm-delete-comment')
@endsection
