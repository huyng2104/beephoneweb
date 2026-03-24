@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold mb-6">Tạo vé hỗ trợ mới</h1>

        <form action="{{ route('admin.support.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold mb-2">Tên khách hàng *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    @error('customer_name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Email *</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    @error('customer_email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Điện thoại</label>
                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    @error('customer_phone')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Chủ đề *</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                    @error('subject')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">Mô tả *</label>
                    <textarea name="description" rows="5" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-2">Danh mục *</label>
                        <select name="category" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            <option value="">Chọn danh mục</option>
                            <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>Thanh toán</option>
                            <option value="warranty" {{ old('category') === 'warranty' ? 'selected' : '' }}>Bảo hành</option>
                            <option value="shipping" {{ old('category') === 'shipping' ? 'selected' : '' }}>Vận chuyển</option>
                            <option value="return" {{ old('category') === 'return' ? 'selected' : '' }}>Trả hàng</option>
                            <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>Chung chung</option>
                            <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('category')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Ưu tiên *</label>
                        <select name="priority" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            <option value="">Chọn ưu tiên</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Thấp</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Trung bình</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Cao</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                        </select>
                        @error('priority')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Gán nhân viên</label>
                        <select name="assigned_to" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="">Không gán</option>
                            {{-- Staff options will be populated here --}}
                        </select>
                        @error('assigned_to')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">
                    Tạo vé
                </button>
                <a href="{{ route('admin.support.index') }}" class="border border-gray-300 text-gray-700 px-6 py-2 rounded font-bold hover:bg-gray-50">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
