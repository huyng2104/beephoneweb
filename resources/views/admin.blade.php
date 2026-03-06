<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ffc105",
                        "background-light": "#f8f8f5",
                        "background-dark": "#231e0f",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-item-active {
            background-color: #ffc105;
            color: #231e0f;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside
            class="w-64 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-background-dark flex flex-col fixed h-full">
            <div class="p-6 flex items-center gap-3">
                <div class="bg-primary rounded-lg p-1.5 flex items-center justify-center">
                    <span class="material-symbols-outlined text-background-dark font-bold">smartphone</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white leading-none">Bee Phone
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Hệ thống quản trị</p>
                </div>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1">
                <a class="sidebar-item-active flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-colors"
                    href="#">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Bảng điều khiển</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    href="#">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span>Sản phẩm</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    href="#">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span>Đơn hàng</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    href="#">
                    <span class="material-symbols-outlined">group</span>
                    <span>Khách hàng</span>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                    href="{{ route('admin.categories.index') }}">
                    <span class="material-symbols-outlined">category</span>
                    <span>Danh mục</span>
                </a>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800">
                    <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        href="#">
                        <span class="material-symbols-outlined">settings</span>
                        <span>Cài đặt</span>
                    </a>
                </div>
            </nav>
            <div class="p-4 bg-slate-50 dark:bg-slate-900 m-4 rounded-xl border border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-300 overflow-hidden"
                        data-alt="Avatar của Alex Johnson quản trị viên"
                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCQ9FLwed6hUAodxd9ykvBX9jnJPa0SIZOAFTt7JD5S5S8LXWLFY62U-5aeNRvaZQetgkhn0Y2YgXmLc89xuKY4atiMN4hOXt6_aM2ursKgGi8pl6Gigoe6gbYZw7-1MfbjHkiROQCGnnfsRHNqbFp0QA_5PHl55Z81GnnMVM0tKXWUQDVpKrueckovvrx3oJwLl0Z1RvjLR5tvPWPMlZX24Up9_TbdPxlcAdiZW0lhBSt-Iyb0xrrtvxktfM33K4G9JbPO05fOiBwn')">
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-semibold truncate">Alex Johnson</p>
                        <p class="text-xs text-slate-500 truncate">Quản trị viên</p>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Header -->
            <header
                class="h-16 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md sticky top-0 z-10 px-8 flex items-center justify-between">
                <div class="w-96">
                    <div class="relative group">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                        <input
                            class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/50 transition-all outline-none"
                            placeholder="Tìm kiếm đơn hàng, khách hàng..." type="text" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors relative">
                        <span class="material-symbols-outlined text-slate-600 dark:text-slate-300">notifications</span>
                        <span
                            class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-background-dark"></span>
                    </button>
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined text-slate-600 dark:text-slate-300">help</span>
                    </button>
                </div>
            </header>
            <!-- Dashboard Content -->
            <div class="p-8 space-y-8">
                <div>
                    <h2 class="text-2xl font-bold">Tổng quan hệ thống</h2>
                    <p class="text-slate-500 dark:text-slate-400">Chào mừng trở lại, hôm nay có 24 đơn hàng mới.</p>
                </div>
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary">payments</span>
                            </div>
                            <span class="text-green-500 text-xs font-bold flex items-center">
                                <span class="material-symbols-outlined text-sm">trending_up</span> 12.5%
                            </span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Tổng doanh thu</p>
                        <h3 class="text-2xl font-bold mt-1">2.450.000.000đ</h3>
                        <div class="mt-4 h-1 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="bg-primary h-full" style="width: 65%;"></div>
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <span
                                    class="material-symbols-outlined text-blue-600 dark:text-blue-400">shopping_bag</span>
                            </div>
                            <span class="text-green-500 text-xs font-bold flex items-center">
                                <span class="material-symbols-outlined text-sm">trending_up</span> 5.2%
                            </span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Tổng đơn hàng</p>
                        <h3 class="text-2xl font-bold mt-1">1.250</h3>
                        <p class="text-xs text-slate-400 mt-2">Đã giao thành công 98%</p>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <span
                                    class="material-symbols-outlined text-purple-600 dark:text-purple-400">person_add</span>
                            </div>
                            <span class="text-green-500 text-xs font-bold flex items-center">
                                <span class="material-symbols-outlined text-sm">trending_up</span> 8.1%
                            </span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Khách hàng mới</p>
                        <h3 class="text-2xl font-bold mt-1">340</h3>
                        <p class="text-xs text-slate-400 mt-2">Tính trong 30 ngày qua</p>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                <span
                                    class="material-symbols-outlined text-orange-600 dark:text-orange-400">package_2</span>
                            </div>
                            <span class="text-red-500 text-xs font-bold flex items-center">
                                <span class="material-symbols-outlined text-sm">trending_down</span> 2.3%
                            </span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Sản phẩm tồn kho</p>
                        <h3 class="text-2xl font-bold mt-1">156</h3>
                        <p class="text-xs text-slate-400 mt-2">Cần nhập thêm 12 mặt hàng</p>
                    </div>
                </div>
                <!-- Charts & Secondary Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Revenue Chart -->
                    <div
                        class="lg:col-span-2 bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-lg font-bold">Doanh thu theo tháng</h3>
                                <p class="text-sm text-slate-500">Tháng này tăng trưởng mạnh nhờ iPhone 15</p>
                            </div>
                            <select
                                class="bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm font-medium px-4 py-2 focus:ring-0">
                                <option>Năm 2024</option>
                                <option>Năm 2023</option>
                            </select>
                        </div>
                        <div class="flex items-end justify-between h-64 gap-2 pt-4 px-4">
                            <div class="flex flex-col items-center gap-2 flex-1 group">
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-t-lg group-hover:bg-primary/40 transition-colors"
                                    style="height: 40%;"></div>
                                <span class="text-xs font-medium text-slate-400">T1</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1 group">
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-t-lg group-hover:bg-primary/40 transition-colors"
                                    style="height: 55%;"></div>
                                <span class="text-xs font-medium text-slate-400">T2</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1 group">
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-t-lg group-hover:bg-primary/40 transition-colors"
                                    style="height: 45%;"></div>
                                <span class="text-xs font-medium text-slate-400">T3</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1 group">
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-t-lg group-hover:bg-primary/40 transition-colors"
                                    style="height: 70%;"></div>
                                <span class="text-xs font-medium text-slate-400">T4</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1 group">
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-t-lg group-hover:bg-primary/40 transition-colors"
                                    style="height: 60%;"></div>
                                <span class="text-xs font-medium text-slate-400">T5</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 flex-1 group">
                                <div class="w-full bg-primary rounded-t-lg shadow-lg shadow-primary/20"
                                    style="height: 90%;"></div>
                                <span class="text-xs font-bold text-primary">T6</span>
                            </div>
                        </div>
                    </div>
                    <!-- Top Selling Products -->
                    <div
                        class="bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
                        <h3 class="text-lg font-bold mb-6">Sản phẩm bán chạy</h3>
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-2">
                                    <div class="w-full h-full bg-contain bg-center bg-no-repeat"
                                        data-alt="iPhone 15 Pro Max màu titan"
                                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCmD4E0iIbxG8CTuzPE8MgrvXwZ2nhlNTlDwAZsZthh5ZYT0_EpgCvq7hV_vFfuqf7J3vO-McjFHazwgZsGFo5RFmcnJxgGQFZ7Gc-fBIqHXYbicm-kQZCyU9vFSJjFyfw6-XvDen8dumtBHY4vm22-HRFjk_Sdnj1I7d_V1Kqcm_ZG6WIYKKZp1gYvS4ne1ghLQ5Yn7-rXEyUdW5V9AcaLw-EZfZfGWnSrQ9GP3VFgH5x1lgBxI9cpb_5vHkTe-uHrJa0AiAQGdjoK')">
                                    </div>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-semibold truncate">iPhone 15 Pro Max</p>
                                    <p class="text-xs text-slate-500">245 lượt bán</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-primary">8.5 tỷ</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-2">
                                    <div class="w-full h-full bg-contain bg-center bg-no-repeat"
                                        data-alt="Samsung Galaxy S24 Ultra"
                                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBYRvkB_YJbbXvzMlRtv-t9ESgGwu64T-msLkKi-fUJwfoeVHfPpnpOt_ssVzp5zDA5p0pfmE27UlH5zVlDpphCB3Ry39OXw_vVmLbpZEWLZqQO0u8-POGxzVKXciqkpxkGUbo0AL2rSWLJfF-4iKmRV7-rf7WC3PbhmSDW6IihLblGpw5d9UT5BKia0mws55RKbXlJmL1GScr_K2f1aWw48o4kDUOgbUyHac0kF7oekfKJHqYkLSD0GEKm6KNCLpp2n5WubAhFWn0Z')">
                                    </div>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-semibold truncate">Samsung S24 Ultra</p>
                                    <p class="text-xs text-slate-500">182 lượt bán</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-primary">5.4 tỷ</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-2">
                                    <div class="w-full h-full bg-contain bg-center bg-no-repeat"
                                        data-alt="MacBook Air M3 2024"
                                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBDbJupRJk_AHEk3Eioso9l3bYyemijYw7x81xkgLB2fliG7SGB1c4ag4lNUvJtHLTrSvs4UOPR5cHUQ-CKxWj5j-RZS53WkhevLS2WouBxR_7dOcxVsF_IZBmCmesn_fh3o4ybERCiJAHPvVG7U-OrGBcOhy_DmQq_iWK7EMJ5sAs7QxMQnOUoTsZ_EfjTy6XSfC0_rTC1wSRep8ZKlRywscWMOCJcjHCfROPwDQRkY3tIef_qTuh1tJiXasb8VK0fNjkA7s2t7gnj')">
                                    </div>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-semibold truncate">MacBook Air M3</p>
                                    <p class="text-xs text-slate-500">95 lượt bán</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-primary">3.2 tỷ</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-2">
                                    <div class="w-full h-full bg-contain bg-center bg-no-repeat"
                                        data-alt="AirPods Pro Gen 2"
                                        style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBMFJKw8tAW7RZgmnqxYyiFgKY9FSU7ruWZIjTfoO9nrij9kknybWt4wDazrbjLZE8chp5zCsUdUZ9Tmixvd2HHbTvhlACGo9aEvsk5yVVq6nQZ0x6k451Xf-G_kMtClO3BZb33b-MfGdKEInbF1vhB5tiyR1qGCxjU3KA4Bb8pw4EihsJfgRdoMCEwQuzCFCeYqln8xT11nbj_F8EhL1MFeQ8ZeiDrZKxeCiHPguurbr_3bstiPeT1MNMX8PkUZ5V2AbsKEYYUFrq0')">
                                    </div>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-semibold truncate">AirPods Pro Gen 2</p>
                                    <p class="text-xs text-slate-500">88 lượt bán</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-primary">450tr</p>
                                </div>
                            </div>
                        </div>
                        <button
                            class="w-full mt-8 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-primary/20 hover:text-primary transition-all rounded-lg text-sm font-semibold">Xem
                            chi tiết</button>
                    </div>
                </div>
                <!-- Latest Orders Table -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-lg font-bold">Đơn hàng mới nhất</h3>
                        <button class="text-sm font-semibold text-primary hover:underline">Xem tất cả</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead
                                class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase">
                                <tr>
                                    <th class="px-6 py-4">Mã đơn</th>
                                    <th class="px-6 py-4">Khách hàng</th>
                                    <th class="px-6 py-4">Ngày đặt</th>
                                    <th class="px-6 py-4">Trạng thái</th>
                                    <th class="px-6 py-4 text-right">Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4 font-semibold">#BP-8821</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-xs font-bold text-primary">
                                                NT</div>
                                            <span>Nguyễn Văn Tú</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">24/05/2024</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            Đang xử lý
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-primary">32.990.000đ</td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4 font-semibold">#BP-8820</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold">
                                                LT</div>
                                            <span>Lê Thị Thu</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">23/05/2024</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            Hoàn thành
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-primary">15.450.000đ</td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4 font-semibold">#BP-8819</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold">
                                                HM</div>
                                            <span>Hoàng Minh</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">23/05/2024</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            Hủy
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-primary">5.200.000đ</td>
                                </tr>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4 font-semibold">#BP-8818</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold">
                                                PA</div>
                                            <span>Phạm An</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">22/05/2024</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            Hoàn thành
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-primary">21.800.000đ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
