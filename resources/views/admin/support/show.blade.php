@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold">{{ $ticket->ticket_code }}</h1>
                <p class="text-gray-500 mt-1">{{ $ticket->subject }}</p>
            </div>
            <a href="{{ route('admin.support.index') }}" class="text-blue-600 hover:text-blue-800">Quay lại danh sách</a>
        </div>

        <!-- Ticket Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Customer Info -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold mb-4">Thông tin khách hàng</h3>
                <p class="text-sm"><strong>Tên:</strong> {{ $ticket->customer_name }}</p>
                <p class="text-sm"><strong>Email:</strong> {{ $ticket->customer_email }}</p>
                <p class="text-sm"><strong>Điện thoại:</strong> {{ $ticket->customer_phone ?? 'N/A' }}</p>
            </div>

            <!-- Ticket Details -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold mb-4">Chi tiết vé</h3>
                <p class="text-sm"><strong>Danh mục:</strong> {{ ucfirst($ticket->category) }}</p>
                <p class="text-sm"><strong>Ưu tiên:</strong> 
                    <span class="px-2 py-1 rounded text-xs font-bold
                        @if($ticket->priority === 'urgent') bg-red-100 text-red-600
                        @elseif($ticket->priority === 'high') bg-orange-100 text-orange-600
                        @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-600
                        @else bg-green-100 text-green-600
                        @endif">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </p>
                <p class="text-sm"><strong>Trạng thái:</strong> 
                    <span class="px-2 py-1 rounded text-xs font-bold
                        @if($ticket->status === 'open') bg-blue-100 text-blue-600
                        @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-600
                        @elseif($ticket->status === 'resolved') bg-green-100 text-green-600
                        @else bg-gray-100 text-gray-600
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </p>
            </div>

            <!-- Staff Assignment -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold mb-4">Gán nhân viên</h3>
                <form action="{{ route('admin.support.assignStaff', $ticket) }}" method="POST">
                    @csrf
                    <select name="assigned_to" class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-2">
                        <option value="">Chọn nhân viên</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" {{ $ticket->assigned_to === $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded text-sm font-bold hover:bg-blue-700">
                        Cập nhật
                    </button>
                </form>
            </div>
        </div>

        <!-- Ticket Description -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h3 class="font-bold mb-4">Mô tả yêu cầu</h3>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
        </div>

        <!-- Responses -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h3 class="font-bold mb-4">Phản hồi ({{ $ticket->response_count }})</h3>
            
            @if($ticket->responses->count() > 0)
                <div class="space-y-4 mb-6">
                    @foreach($ticket->responses as $response)
                        <div class="border-l-4 {{ $response->is_staff_response ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }} p-4 rounded">
                            <div class="flex items-center justify-between mb-2">
                                <strong>{{ $response->user->name }} @if($response->is_staff_response) <span class="text-xs bg-blue-600 text-white px-2 py-0.5 rounded">STAFF</span>@endif</strong>
                                <span class="text-xs text-gray-500">{{ $response->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $response->message }}</p>
                            @if($response->attachment_path)
                                <a href="{{ asset('storage/' . $response->attachment_path) }}" class="text-blue-600 text-sm mt-2 inline-block">📎 Tải tệp đính kèm</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm mb-6">Chưa có phản hồi nào</p>
            @endif

            <!-- Add Response Form -->
            <form action="{{ route('admin.support.addResponse', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-4 border-t">
                @csrf
                <div>
                    <label class="block text-sm font-bold mb-2">Phản hồi của bạn</label>
                    <textarea name="message" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Nhập phản hồi..." required></textarea>
                    @error('message')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Đính kèm tệp (tuỳ chọn)</label>
                    <input type="file" name="attachment_path" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-blue-700">
                    Gửi phản hồi
                </button>
            </form>
        </div>

        <!-- Status Update -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold mb-4">Cập nhật trạng thái</h3>
            <form action="{{ route('admin.support.updateStatus', $ticket) }}" method="POST" class="flex gap-2 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-bold mb-2">Trạng thái</label>
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Mới</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                </div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-green-700">
                    Cập nhật
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
