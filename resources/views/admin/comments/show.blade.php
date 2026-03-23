@extends('admin.layouts.app')

@section('content')
    <div class="bg-background-light text-gray-800 min-h-screen antialiased font-sans">
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.comments.index', ['product_id' => $comment->product_id]) }}"
                        class="inline-flex items-center justify-center rounded-lg bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors border border-slate-200">
                        Back
                    </a>
                    <h1 class="text-xl font-bold text-gray-900 leading-tight flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">chat</span>
                        Comment #{{ $comment->id }}
                    </h1>
                </div>

                <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST"
                    onsubmit="return confirm('Delete this comment?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                        Delete
                    </button>
                </form>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">User</p>
                        <p class="text-sm font-semibold text-slate-800 mt-1">
                            {{ $comment->user?->name ?? $comment->guest_name ?? 'Guest' }}
                        </p>
                        @if($comment->user?->email || $comment->guest_email)
                            <p class="text-xs text-slate-500 mt-1">{{ $comment->user?->email ?? $comment->guest_email }}</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Product</p>
                        <p class="text-sm font-semibold text-slate-800 mt-1">
                            @if($comment->product)
                                <a class="text-blue-600 hover:text-blue-800 transition-colors"
                                    href="{{ route('admin.comments.index', ['product_id' => $comment->product->id]) }}">
                                    {{ $comment->product->name }}
                                </a>
                            @else
                                <span class="text-slate-400 italic">Deleted product</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Rating</p>
                        <p class="text-sm font-semibold text-slate-800 mt-1">{{ $comment->rating ?? '—' }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $comment->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($comment->parent)
                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Reply to</p>
                        <p class="text-sm text-slate-700 mt-2">{{ $comment->parent->content }}</p>
                    </div>
                @endif

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Content</p>
                    <p class="text-sm text-slate-800 mt-2 whitespace-pre-line">{{ $comment->content }}</p>
                </div>

                @if($comment->image_path)
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Image</p>
                        <img src="{{ asset('storage/' . $comment->image_path) }}" alt="comment image"
                            class="mt-2 max-w-full md:max-w-md rounded-xl border border-slate-200 object-cover bg-slate-50">
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Replies</h2>
                    <span class="text-xs font-bold text-slate-500">{{ $comment->children->count() }}</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($comment->children as $reply)
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $reply->user?->name ?? $reply->guest_name ?? 'Guest' }}
                                        <span class="text-xs font-semibold text-slate-400 ml-2">#{{ $reply->id }}</span>
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $reply->created_at?->format('d/m/Y H:i') }}</p>
                                </div>
                                <form action="{{ route('admin.comments.destroy', $reply) }}" method="POST"
                                    onsubmit="return confirm('Delete this comment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                        Delete
                                    </button>
                                </form>
                            </div>

                            <div class="mt-3 text-sm text-slate-700 whitespace-pre-line">{{ $reply->content }}</div>

                            @if($reply->image_path)
                                <img src="{{ asset('storage/' . $reply->image_path) }}" alt="comment image"
                                    class="mt-3 max-w-full md:max-w-sm rounded-xl border border-slate-200 object-cover bg-slate-50">
                            @endif
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400">No replies.</div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>
@endsection
