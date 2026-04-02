@extends('client.layouts.app')

@section('title','Liên hệ & Hỗ trợ')

@section('content')

<main class="max-w-[1200px] mx-auto px-4 lg:px-10 py-10">

<div class="mb-10 text-center max-w-2xl mx-auto">
<h1 class="text-4xl lg:text-5xl font-black mb-4">
Chúng tôi có thể giúp gì cho bạn?
</h1>

<p class="text-gray-500 text-lg">
Tìm kiếm câu trả lời nhanh chóng hoặc gửi yêu cầu hỗ trợ cho Bee Phone
</p>
</div>


{{-- SEARCH --}}
<div class="max-w-3xl mx-auto mb-16">
<label class="flex flex-col h-14 w-full shadow-lg rounded-xl overflow-hidden">
<div class="flex items-center h-full bg-white dark:bg-white/5">

<span class="material-symbols-outlined px-4 text-gray-400">
search
</span>

<input
class="w-full bg-transparent focus:outline-none text-lg"
placeholder="Tìm kiếm câu hỏi thường gặp..."
>
</div>
</label>
</div>


{{-- FAQ --}}
<section class="mb-20">

<div class="flex justify-center items-center gap-2 mb-8">
<span class="material-symbols-outlined text-primary">
help_center
</span>

<h2 class="text-2xl font-bold">
Câu hỏi thường gặp (FAQ)
</h2>

</div>

<div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-4">

<details class="border rounded-xl p-4 bg-white dark:bg-white/5">
<summary class="font-semibold cursor-pointer">
🚚 Giao hàng
</summary>

<p class="text-gray-500 mt-3 text-sm">
Bee Phone hỗ trợ giao nhanh trong 2h tại Hà Nội và TP.HCM.
Các tỉnh khác từ 2-4 ngày.
</p>

</details>


<details class="border rounded-xl p-4 bg-white dark:bg-white/5">
<summary class="font-semibold cursor-pointer">
🔁 Đổi trả
</summary>

<p class="text-gray-500 mt-3 text-sm">
Đổi trả trong 30 ngày nếu lỗi từ nhà sản xuất.
</p>

</details>


<details class="border rounded-xl p-4 bg-white dark:bg-white/5">
<summary class="font-semibold cursor-pointer">
🛡 Bảo hành
</summary>

<p class="text-gray-500 mt-3 text-sm">
Sản phẩm được bảo hành chính hãng 12 tháng.
</p>

</details>


<details class="border rounded-xl p-4 bg-white dark:bg-white/5">
<summary class="font-semibold cursor-pointer">
💳 Thanh toán
</summary>

<p class="text-gray-500 mt-3 text-sm">
Chấp nhận VISA, MasterCard, MoMo, ZaloPay và chuyển khoản.
</p>

</details>

</div>

</section>


{{-- CONTACT FORM --}}
<div class="grid lg:grid-cols-5 gap-10">

<div class="lg:col-span-3 bg-white dark:bg-white/5 p-8 rounded-2xl border">

<h3 class="text-2xl font-bold mb-6">
Gửi yêu cầu hỗ trợ
</h3>

@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form class="space-y-5" method="POST" action="{{ route('contact.submit') }}">
    @csrf

    <div class="grid md:grid-cols-2 gap-5">

        <input
            name="name"
            value="{{ old('name') }}"
            class="border rounded-lg px-4 py-3"
            placeholder="Họ và tên">

        <input
            name="email"
            value="{{ old('email') }}"
            class="border rounded-lg px-4 py-3"
            placeholder="Email">

    </div>


<div class="grid md:grid-cols-2 gap-5">

    <input
        name="phone"
        value="{{ old('phone') }}"
        class="border rounded-lg px-4 py-3"
        placeholder="Số điện thoại">

    <select
        name="category"
        class="border rounded-lg px-4 py-3">

        <option value="Tư vấn mua hàng" {{ old('category') == 'Tư vấn mua hàng' ? 'selected' : '' }}>Tư vấn mua hàng</option>
        <option value="Hỗ trợ kỹ thuật" {{ old('category') == 'Hỗ trợ kỹ thuật' ? 'selected' : '' }}>Hỗ trợ kỹ thuật</option>
        <option value="Bảo hành" {{ old('category') == 'Bảo hành' ? 'selected' : '' }}>Bảo hành</option>

    </select>

</div>


<textarea
    name="message"
    class="border rounded-lg px-4 py-3 w-full"
    rows="5"
    placeholder="Bạn cần hỗ trợ gì?">{{ old('message') }}</textarea>


<button
    type="submit"
    class="bg-primary px-8 py-4 rounded-xl font-bold">

    Gửi yêu cầu

</button>

</form>

</div>


{{-- CONTACT INFO --}}
<div class="lg:col-span-2 space-y-6">

<div class="bg-white dark:bg-white/5 p-8 rounded-2xl border">

<h3 class="text-xl font-bold mb-6">
Liên hệ trực tiếp
</h3>


<div class="space-y-5">

<div>
<p class="text-sm text-gray-400">
Hotline
</p>

<p class="font-bold">
1900 8888
</p>

</div>


<div>
<p class="text-sm text-gray-400">
Email
</p>

<p class="font-bold">
support@beephone.vn
</p>

</div>


<div>
<p class="text-sm text-gray-400">
Địa chỉ
</p>

<p class="font-bold">
123 Đường Láng, Hà Nội
</p>

</div>

</div>

</div>

</div>

</div>

</main>

@endsection