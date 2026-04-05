@extends('client.layouts.app')

@section('title','Liên hệ & Hỗ trợ')

@section('content')

<main class="max-w-[1320px] mx-auto px-4 lg:px-10 py-10">

<div class="mb-8 text-center max-w-3xl mx-auto">
    <h1 class="text-4xl lg:text-5xl font-black mb-4">
        Chúng tôi có thể giúp gì cho bạn?
    </h1>
    <p class="text-gray-500 text-lg">
        Tìm kiếm câu trả lời nhanh chóng hoặc gửi yêu cầu hỗ trợ trực tiếp cho đội ngũ Bee Phone
    </p>
</div>

<div class="max-w-4xl mx-auto mb-14">
    <div class="flex items-center rounded-xl shadow-lg bg-white overflow-hidden h-14">
        <span class="material-symbols-outlined px-4 text-gray-400">search</span>
        <input
            class="w-full h-full text-lg px-2 focus:outline-none"
            placeholder="Tìm kiếm câu hỏi thường gặp..."
            type="text"
        >
    </div>
</div>


{{-- FAQ --}}
<section class="mb-12">

    <div class="flex justify-center items-center gap-2 mb-6">
        <span class="material-symbols-outlined text-primary">help_center</span>
        <h2 class="text-2xl font-bold">Câu hỏi thường gặp (FAQ)</h2>
    </div>

    <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border">
            <h3 class="flex items-center gap-2 text-base font-semibold mb-2">🚚 Giao hàng</h3>
            <p class="text-gray-500 text-sm">Bee Phone hỗ trợ giao nhanh trong 2h tại Hà Nội và TP.HCM, tỉnh khác 2-4 ngày.</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border">
            <h3 class="flex items-center gap-2 text-base font-semibold mb-2">🔁 Đổi trả</h3>
            <p class="text-gray-500 text-sm">Đổi trả trong 30 ngày nếu lỗi từ nhà sản xuất, miễn phí vận chuyển đơn nội thành.</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border">
            <h3 class="flex items-center gap-2 text-base font-semibold mb-2">🛡 Bảo hành</h3>
            <p class="text-gray-500 text-sm">Sản phẩm được bảo hành chính hãng 12 tháng; bảo hành 1 đổi 1 nếu lỗi nhà sản xuất.</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border">
            <h3 class="flex items-center gap-2 text-base font-semibold mb-2">💳 Thanh toán</h3>
            <p class="text-gray-500 text-sm">Chấp nhận Visa, MasterCard, MoMo, ZaloPay và chuyển khoản ngân hàng.</p>
        </div>
    </div>

</section>

{{-- CONTACT FORM + INFO --}}
<div class="grid lg:grid-cols-10 gap-6">

    <div class="lg:col-span-6 bg-white rounded-2xl shadow-md border p-8">
        <h3 class="text-[22px] font-black mb-4">Gửi yêu cầu hỗ trợ</h3>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
                <ul class="list-disc pl-5 text-sm">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form class="space-y-4" method="POST" action="{{ route('contact.submit') }}">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <input name="name" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Họ và tên" />
                <input name="email" value="{{ old('email') }}" type="email" class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Email" />
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <input name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Số điện thoại" />
                <select name="category" class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="Tư vấn mua hàng" {{ old('category') == 'Tư vấn mua hàng' ? 'selected' : '' }}>Tư vấn mua hàng</option>
                    <option value="Hỗ trợ kỹ thuật" {{ old('category') == 'Hỗ trợ kỹ thuật' ? 'selected' : '' }}>Hỗ trợ kỹ thuật</option>
                    <option value="Bảo hành" {{ old('category') == 'Bảo hành' ? 'selected' : '' }}>Bảo hành</option>
                </select>
            </div>

            <textarea name="message" class="w-full rounded-lg border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary" rows="5" placeholder="Bạn cần hỗ trợ gì?">{{ old('message') }}</textarea>

            <button type="submit" class="w-full bg-primary text-white font-semibold rounded-xl py-3 hover:bg-primary-dark transition">Gửi yêu cầu</button>
        </form>
    </div>

    <div class="lg:col-span-4 space-y-5">
        <div class="bg-white rounded-2xl shadow-md border p-6">
            <h3 class="text-xl font-bold mb-4">Liên hệ trực tiếp</h3>

            <div class="space-y-4 text-sm text-gray-600">
                <div>
                    <p class="text-gray-400">Hotline 24/7</p>
                    <p class="font-semibold text-lg">1900 8888</p>
                </div>
                <div>
                    <p class="text-gray-400">Email hỗ trợ</p>
                    <p class="font-semibold text-lg">support@beephone.vn</p>
                </div>
                <div>
                    <p class="text-gray-400">Địa chỉ</p>
                    <p class="font-semibold text-lg">123 Đường Láng, Quận Đống Đa, Hà Nội</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md border overflow-hidden h-[260px]">
            <iframe class="w-full h-full" src="https://maps.google.com/maps?q=123+Duong+Lang+Ha+Noi&output=embed" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>

</div>

</main>


@endsection