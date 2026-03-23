@extends('admin.layouts.app')

@section('content')
<div class="bg-background-light text-gray-800 min-h-screen antialiased font-sans">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">chat</span>
                    Manage Comments
                </h1>
            </div>
            <div class="text-sm text-slate-500 font-semibold">
                Total: {{ $comments->count() }} comments
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Average rating</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $averageRating }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total ratings</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalRatings }}</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">5sao / 4sao / 3sao / 2sao / 1sao</p>
                <p class="text-sm font-semibold text-slate-700 mt-2">
                    {{ $ratingBreakdown[5] ?? 0 }} / {{ $ratingBreakdown[4] ?? 0 }} / {{ $ratingBreakdown[3] ?? 0 }} / {{ $ratingBreakdown[2] ?? 0 }} / {{ $ratingBreakdown[1] ?? 0 }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">User</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Image</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Content</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($comments as $comment)
                            <tr id="comment-{{ $comment->id }}" class="hover:bg-yellow-50/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-slate-700">#{{ $comment->id }}</td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @if($comment->parent_id)
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex w-fit items-center rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-bold text-blue-700">
                                                Reply
                                            </span>
                                            <span class="text-xs text-slate-400">
                                                Reply to #{{ $comment->parent_id }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-[11px] font-bold text-green-700">
                                            Top-level comment
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 font-medium">
                                    {{ $comment->user?->name ?? $comment->guest_name ?? 'Guest' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($comment->product)
                                        <a href="{{ route('admin.comments.index', ['product_id' => $comment->product->id]) }}" class="font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                            {{ $comment->product->name }}
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic">Deleted product</span>
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
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.comments.show', $comment) }}" class="inline-flex items-center justify-center rounded-lg bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors border border-slate-200">
                                        View
                                    </a>
                                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors border border-red-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-gray-400 bg-gray-50/50">
                                    <span class="material-symbols-outlined text-5xl mb-3 text-gray-300 block">chat</span>
                                    <p class="text-sm font-medium text-gray-500">No comments yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection


