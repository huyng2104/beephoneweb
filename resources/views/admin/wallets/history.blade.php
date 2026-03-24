@extends('admin.layouts.app')

@section('content')
<div class="p-8 bg-[#F8F9FA] min-h-screen">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <div class="flex items-center gap-2 text-slate-400 text-xs font-black uppercase tracking-widest mb-2">
                <a href="{{ route('admin.wallets.index') }}" class="hover:text-bee">Quản lý ví</a>
                <span>/</span>
                <span class="text-slate-900">Chi tiết lịch sử</span>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Lịch sử: <span class="text-[#FFC107]">{{ $user->name }}</span></h1>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="bg-white px-6 py-4 rounded-[1.5rem] shadow-sm border border-slate-100 text-right">
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Số dư hiện tại</p>
                <p class="text-xl font-black text-emerald-600">{{ number_format($wallet->balance, 0, ',', '.') }} đ</p>
            </div>
            <a href="{{ route('admin.wallets.index') }}" class="p-4 bg-white text-slate-400 hover:text-bee rounded-2xl shadow-sm border border-slate-100 transition-all">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Thời gian</th>
                    <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase text-center tracking-widest">Loại</th>
                    <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase text-right tracking-widest">Số tiền</th>
                    <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Biến động</th>
                    <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Ghi chú</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($transactions as $tx)
                <tr class="hover:bg-slate-50/50 transition-all">
                    <td class="px-8 py-5 text-sm font-bold text-slate-500">
                        {{ $tx->created_at->format('d/m/Y') }} <br>
                        <span class="text-[10px] font-medium opacity-60">{{ $tx->created_at->format('H:i:s') }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        @php
                            $colors = [
                                'deposit' => 'bg-emerald-100 text-emerald-600',
                                'payment' => 'bg-orange-100 text-orange-600',
                                'withdraw' => 'bg-rose-100 text-rose-600',
                                'refund' => 'bg-blue-100 text-blue-600'
                            ];
                            $labels = [
                                'deposit' => 'Nạp tiền',
                                'payment' => 'Thanh toán',
                                'withdraw' => 'Trừ tiền',
                                'refund' => 'Hoàn tiền'
                            ];
                        @endphp
                        <span class="px-3 py-1 {{ $colors[$tx->type] }} text-[10px] font-black uppercase rounded-lg">
                            {{ $labels[$tx->type] }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right font-black {{ in_array($tx->type, ['deposit', 'refund']) ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ in_array($tx->type, ['deposit', 'refund']) ? '+' : '-' }} {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-8 py-5 text-xs font-bold text-slate-400 italic">
                        {{ number_format($tx->balance_before, 0, ',', '.') }} → {{ number_format($tx->balance_after, 0, ',', '.') }}
                    </td>
                    <td class="px-8 py-5 text-sm font-bold text-slate-600">{{ $tx->description }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center font-bold text-slate-300 uppercase tracking-widest">Không có dữ liệu giao dịch</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-8 bg-slate-50/30">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection