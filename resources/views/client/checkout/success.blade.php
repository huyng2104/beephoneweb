@extends('client.layouts.app')
@section('title', 'Bee Phone - Đặt hàng thành công')
@section('content')
<main class="min-h-[70vh] flex items-center justify-center p-6">
    <div class="bg-white dark:bg-white/5 p-10 rounded-3xl shadow-xl text-center max-w-lg border border-gray-100 dark:border-white/10">
        <div class="w-24 h-24 bg-green-100 dark:bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-6xl">verified</span>
        </div>
        <h1 class="text-3xl font-black mb-4 text-[#181611] dark:text-white">Tuyệt vời!</h1>
        <p class="text-gray-600 dark:text-gray-300 text-lg mb-8">{{ session('success') }}</p>
        <p class="text-sm text-gray-500 mb-8">Chúng tôi sẽ sớm liên hệ với bạn để xác nhận đơn hàng và tiến hành giao hàng trong thời gian sớm nhất.</p>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-primary text-black font-bold px-8 py-4 rounded-xl hover:scale-105 transition-transform shadow-lg">
            <span class="material-symbols-outlined">home</span> Về trang chủ
        </a>
    </div>
</main>
@endsection