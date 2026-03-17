@extends('admin.layouts.app') 

@section('content')
<script>
    function openModalWallet(id) {
        const overlay = document.getElementById('modalOverlay' + id);
        const container = document.getElementById('modalContainer' + id);
        if(overlay && container) {
            overlay.classList.remove('hidden');
            overlay.style.display = 'flex';
            setTimeout(() => {
                overlay.style.opacity = '1';
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            }, 10);
        }
    }

    function closeModalWallet(id) {
        const overlay = document.getElementById('modalOverlay' + id);
        const container = document.getElementById('modalContainer' + id);
        if(overlay && container) {
            container.classList.remove('scale-100');
            container.classList.add('scale-95');
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.add('hidden');
            }, 300);
        }
    }
</script>

<style>
    .bg-bee { background-color: #FFC107 !important; }
    .text-bee { color: #FFC107 !important; }
    .wallet-modal-overlay {
        position: fixed !important; inset: 0 !important; z-index: 99999 !important;
        background-color: rgba(15, 23, 42, 0.6) !important; backdrop-filter: blur(4px) !important;
        display: none; align-items: center; justify-content: center; opacity: 0; transition: all 0.3s;
    }
</style>

<div class="p-8 bg-[#F8F9FA] min-h-screen">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản Lý Ví Tiền</h1>
            <p class="text-slate-500 font-medium italic">Hệ thống tài chính Bee Phone Admin</p>
        </div>
        
        <form action="{{ route('admin.wallets.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm tên hoặc email khách hàng..." 
                       class="block w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-bee outline-none shadow-sm transition-all">
            </div>
            <button type="submit" class="px-6 py-3 bg-slate-900 text-white font-black rounded-2xl hover:bg-slate-800 transition-all text-sm shadow-md">
                TÌM
            </button>
            @if(request('search'))
                <a href="{{ route('admin.wallets.index') }}" class="px-4 py-3 bg-white text-slate-400 font-bold rounded-2xl hover:text-rose-500 transition-all text-sm border border-slate-200 shadow-sm">
                    XÓA
                </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-100 text-emerald-700 rounded-2xl font-bold shadow-sm border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @php
        $totalBalance = \App\Models\Wallet::sum('balance');
        $todayDeposit = \App\Models\WalletTransaction::where('type', 'deposit')->whereDate('created_at', today())->sum('amount');
        $todayWithdraw = \App\Models\WalletTransaction::whereIn('type', ['withdraw', 'payment'])->whereDate('created_at', today())->sum('amount');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-white">
        <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl shadow-slate-200">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Tổng quỹ hệ thống</p>
            <h3 class="text-2xl font-black mt-1">{{ number_format($totalBalance, 0, ',', '.') }} đ</h3>
        </div>
        <div class="bg-emerald-500 p-6 rounded-[2rem] shadow-xl shadow-emerald-100">
            <p class="text-emerald-100 text-[10px] font-black uppercase tracking-widest">Nạp tiền hôm nay</p>
            <h3 class="text-2xl font-black mt-1">+{{ number_format($todayDeposit, 0, ',', '.') }} đ</h3>
        </div>
        <div class="bg-rose-500 p-6 rounded-[2rem] shadow-xl shadow-rose-100">
            <p class="text-rose-100 text-[10px] font-black uppercase tracking-widest">Chi tiêu hôm nay</p>
            <h3 class="text-2xl font-black mt-1">-{{ number_format($todayWithdraw, 0, ',', '.') }} đ</h3>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">Khách hàng</th>
                    <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase text-right tracking-widest">Số dư ví</th>
                    <th class="px-8 py-5 text-xs font-black text-slate-400 uppercase text-center tracking-widest">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50/80 transition-all">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-md">
                                @else
                                    <div class="w-12 h-12 rounded-2xl bg-bee text-white flex items-center justify-center font-black shadow-lg shadow-bee/30">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div>
                                <div class="font-black text-slate-800">{{ $user->name }}</div>
                                <div class="text-xs text-slate-400 font-bold">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="text-lg font-black text-emerald-600">
                            {{ $user->wallet ? number_format($user->wallet->balance, 0, ',', '.') : '0' }} <span class="text-xs opacity-60 font-bold">đ</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('admin.wallets.history', $user->id) }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-black rounded-xl hover:bg-slate-200 transition-all">Lịch sử</a>
                            <button type="button" onclick="openModalWallet('{{ $user->id }}')" class="px-4 py-2 bg-bee text-white text-xs font-black rounded-xl shadow-lg shadow-bee/20 hover:scale-105 transition-all">
                                Nạp/Trừ
                            </button>
                        </div>
                    </td>
                </tr>

                <div class="wallet-modal-overlay hidden" id="modalOverlay{{ $user->id }}" onclick="closeModalWallet('{{ $user->id }}')">
                    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-all" id="modalContainer{{ $user->id }}" onclick="event.stopPropagation()">
                        <form action="{{ route('admin.wallets.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="p-8 border-b border-slate-50 flex justify-between items-center text-left">
                                <h3 class="text-xl font-black text-slate-800">Cập nhật ví</h3>
                                <button type="button" onclick="closeModalWallet('{{ $user->id }}')" class="text-slate-300 hover:text-slate-900 text-2xl font-bold">&times;</button>
                            </div>
                            <div class="p-8 space-y-5 text-left">
                                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Chủ ví</p>
                                    <p class="font-bold text-slate-700">{{ $user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Hình thức giao dịch</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="flex items-center justify-center p-4 border-2 rounded-2xl cursor-pointer transition-all has-[:checked]:border-bee has-[:checked]:bg-bee/5">
                                            <input checked class="hidden" name="action" type="radio" value="add"/>
                                            <span class="text-sm font-black text-slate-700">+ Cộng tiền</span>
                                        </label>
                                        <label class="flex items-center justify-center p-4 border-2 rounded-2xl cursor-pointer transition-all has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50">
                                            <input class="hidden" name="action" type="radio" value="subtract"/> 
                                            <span class="text-sm font-black text-slate-700">- Trừ tiền</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Số tiền (VNĐ)</label>
                                    <input required name="amount" class="block w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-bee font-black text-xl outline-none" placeholder="0" type="number" min="1000">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-400 uppercase mb-2">Lý do/Ghi chú</label>
                                    <textarea name="description" rows="2" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-bee font-medium outline-none" placeholder="Ví dụ: Nạp tiền quà tặng đồ án..."></textarea>
                                </div>
                            </div>
                            <div class="p-8 pt-0 flex gap-3">
                                <button type="button" onclick="closeModalWallet('{{ $user->id }}')" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-2xl uppercase text-xs tracking-widest hover:bg-slate-200">Hủy</button>
                                <button type="submit" class="flex-1 py-4 bg-bee text-white font-black rounded-2xl uppercase text-xs tracking-widest shadow-lg shadow-bee/30 hover:bg-beeDark">Xác nhận</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="3" class="px-8 py-20 text-center text-slate-400 font-bold uppercase tracking-widest">
                        Không tìm thấy khách hàng nào khớp với từ khóa
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-8 bg-slate-50/30">
            {{ $users->links() }}
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Kiểm tra nếu có lỗi từ session
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Ối! Không đủ tiền',
            text: '{{ session('error') }}',
            confirmButtonText: 'Đã hiểu',
            confirmButtonColor: '#FFC107', // Màu vàng Bee
            background: '#fff',
            borderRadius: '2rem',
            customClass: {
                title: 'font-black text-slate-800',
                confirmButton: 'rounded-xl font-bold uppercase tracking-widest px-8 py-3'
            }
        });
    @endif

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false,
            borderRadius: '2rem'
        });
    @endif
</script>
@endsection