@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl">
        <h1 class="text-3xl font-bold mb-6">Chỉnh sửa vé hỗ trợ #{{ $ticket->ticket_code }}</h1>

        <form action="{{ route('admin.support.update', $ticket) }}" method="POST" class="bg-white p-6 rounded-lg shadow">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold mb-2">Trạng thái</label>
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Mới</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Ưu tiên</label>
                    <select name="priority" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Thấp</option>
                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>Cao</option>
                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                    </select>
                    @error('priority')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Gán cho nhân viên</label>
                    <select name="assigned_to" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">Không gán</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" {{ $ticket->assigned_to == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Đánh giá hài lòng (1-5)</label>
                    <input type="number" name="satisfaction_rating" min="1" max="5" value="{{ $ticket->satisfaction_rating }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @error('satisfaction_rating')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Bình luận hài lòng</label>
                <textarea name="satisfaction_comment" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Nhập bình luận (tuỳ chọn)"></textarea>
                @error('satisfaction_comment')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">
                    Lưu thay đổi
                </button>
                <a href="{{ route('admin.support.show', $ticket) }}" class="border border-gray-300 text-gray-700 px-6 py-2 rounded font-bold hover:bg-gray-50">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
