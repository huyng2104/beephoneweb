@extends('admin.layouts.app')

@section('content')
<div class="p-8 space-y-8">
    
    @if(session('success'))
        <div class="p-4 mb-4 text-green-800 bg-green-100 rounded-xl font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-4 text-red-800 bg-red-100 rounded-xl font-bold">{{ session('error') }}</div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Sao kê Quỹ Hệ thống (Ví Tổng)</h2>
            <p class="text-slate-500 text-sm mt-1">Nơi minh bạch mọi dòng tiền công ty chi ra cho khách hàng.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-black text-white p-8 rounded-2xl relative overflow-hidden shadow-xl lg:col-span-1">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-yellow-500 opacity-20 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <p class="text-slate-400 text-sm font-medium mb-2">Tổng tiền Hệ thống đã xuất</p>
                <h2 class="text-4xl font-bold tracking-tight mb-6">
                    {{ number_format($systemWallet->balance, 0, ',', '.') }}<span class="text-xl ml-1 text-yellow-500">đ</span>
                </h2>
                <div class="p-3 bg-white/10 rounded-lg border border-white/20">
                    <p class="text-xs text-slate-300 italic">
                        *Lưu ý: Số dư này thường là số âm, thể hiện khoản tiền công ty đã trích ra để tặng/hỗ trợ khách hàng.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 lg:col-span-2">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-yellow-500">send_money</span> Bơm tiền cho Khách hàng
            </h3>
            
            <form action="{{ route('admin.system_wallet.add_money') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Chọn Khách hàng *</label>
                        <select name="user_id" required class="w-full rounded-lg border-slate-300 focus:border-yellow-500 focus:ring-yellow-500 dark:bg-slate-800 dark:border-slate-700">
                            <option value="">-- Tìm khách hàng --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Số tiền (VNĐ) *</label>
                        <input type="number" name="amount" min="1000" required class="w-full rounded-lg border-slate-300 focus:border-yellow-500 focus:ring-yellow-500 dark:bg-slate-800 dark:border-slate-700" placeholder="Ví dụ: 50000">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Lý do bơm tiền (Kế toán sẽ kiểm tra) *</label>
                    <input type="text" name="description" required class="w-full rounded-lg border-slate-300 focus:border-yellow-500 focus:ring-yellow-500 dark:bg-slate-800 dark:border-slate-700" placeholder="Ví dụ: Hoàn tiền đơn hàng lỗi #ORD-123">
                </div>
                <div class="text-right">
                    <button type="submit" onclick="return confirm('Xác nhận chuyển tiền cho khách? Lệnh này sẽ được ghi vào sao kê hệ thống.')" class="bg-yellow-500 text-black font-bold px-6 py-2.5 rounded-lg hover:bg-yellow-400 transition-colors">
                        Xác nhận chuyển
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white">Lịch sử Sao kê Ví Tổng</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Thời gian</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Giao dịch</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Số tiền</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Lý do chi</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Số dư quỹ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                            {{ $tx->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 text-sm">{!! $tx->type_transaction !!}</td>
                        <td class="px-6 py-4 text-sm font-bold text-red-500">
                            - {{ number_format($tx->amount) }}đ
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                            {{ $tx->description }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-800 dark:text-white">
                            {{ number_format($tx->balance_after) }}đ
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">Hệ thống chưa chi đồng nào!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection