@extends('admin.layouts.app')
@section('content')
    <!DOCTYPE html>

    <html class="light" lang="vi">

    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap"
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
                            "on-surface": "#181611",
                            "outline-variant": "#e6e3db",
                            "surface-bright": "#ffffff",
                            "on-primary-container": "#684f00",
                            "surface": "#ffffff",
                            "on-tertiary-fixed": "#001f28",
                            "error-container": "#ffdad6",
                            "secondary-fixed-dim": "#e6e3db",
                            "inverse-primary": "#f3bf24",
                            "on-secondary-fixed": "#181611",
                            "on-tertiary-fixed-variant": "#004e5f",
                            "surface-container-high": "#ebe9e6",
                            "on-tertiary": "#ffffff",
                            "tertiary-container": "#47d7ff",
                            "on-surface-variant": "#5e5a4d",
                            "error": "#e71408",
                            "inverse-on-surface": "#faefdf",
                            "tertiary-fixed": "#b5ebff",
                            "primary-fixed": "#fef4d4",
                            "on-primary-fixed-variant": "#594400",
                            "surface-container-lowest": "#ffffff",
                            "on-secondary": "#ffffff",
                            "on-primary": "#ffffff",
                            "inverse-surface": "#353025",
                            "surface-dim": "#e6e3db",
                            "surface-tint": "#f4c025",
                            "primary-container": "#fef4d4",
                            "primary-fixed-dim": "#f3bf24",
                            "outline": "#8a8060",
                            "tertiary-fixed-dim": "#46d6fe",
                            "surface-variant": "#f5f3f0",
                            "surface-container-low": "#fcfcfc",
                            "secondary": "#8a8060",
                            "on-secondary-fixed-variant": "#5e5a4d",
                            "on-primary-fixed": "#251a00",
                            "primary": "#f4c025",
                            "secondary-container": "#f5f3f0",
                            "secondary-fixed": "#f5f3f0",
                            "on-tertiary-container": "#005b6f",
                            "background": "#f8f8f5",
                            "on-secondary-container": "#181611",
                            "surface-container-highest": "#e6e3db",
                            "on-error": "#ffffff",
                            "on-background": "#181611",
                            "tertiary": "#00677e",
                            "surface-container": "#f5f3f0",
                            "on-error-container": "#93000a"
                        },
                        fontFamily: {
                            "headline": ["Manrope"],
                            "body": ["Manrope"],
                            "label": ["Manrope"]
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
                font-family: 'Manrope', sans-serif;
            }

            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
                vertical-align: middle;
            }

            .active-nav-border {
                border-right: 4px solid #f4c025;
            }
        </style>
    </head>

    <body class="bg-background text-on-surface antialiased">
        <!-- Main Content Area -->
        <main class="min-h-screen">
            <!-- Top App Bar Shell -->
            <div class="p-8 mx-auto">
                <!-- Breadcrumbs & Title -->
                <div class="mb-8">
                    <div class="flex justify-between items-end">
                        <div>
                            <h2 class="text-2xl font-extrabold text-on-surface tracking-tight">Quản lý yêu cầu hỗ trợ</h2>
                            <p class="text-sm text-on-surface-variant">Theo dõi và xử lý các phản hồi từ khách hàng Bee
                                Phone.</p>
                        </div>
                        <button
                            class="bg-primary hover:bg-amber-400 text-on-primary-fixed font-bold py-2.5 px-6 rounded-xl shadow-sm transition-all active:scale-[0.98] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm" data-icon="download">download</span>
                            Xuất báo cáo
                        </button>
                    </div>
                </div>
                <!-- Summary Cards: Bento Grid Style -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div
                        class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-zinc-100 hover:border-primary transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 bg-zinc-50 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-zinc-400" data-icon="all_inbox">all_inbox</span>
                            </div>
                            <span
                                class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-tighter">+12.5%
                                tháng này</span>
                        </div>
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Tổng yêu cầu</p>
                        <h3 class="text-3xl font-extrabold text-zinc-900">{{ number_format($totalTickets) }}</h3>
                    </div>
                    <div
                        class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-zinc-100 hover:border-amber-500 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-amber-500"
                                    data-icon="pending_actions">pending_actions</span>
                            </div>
                            <span
                                class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-tighter">Mới
                                nhất</span>
                        </div>
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Chờ xử lý</p>
                        <h3 class="text-3xl font-extrabold text-zinc-900">{{ $pendingTickets }}</h3>
                    </div>
                    <div
                        class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-zinc-100 hover:border-tertiary transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 bg-tertiary/10 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-tertiary" data-icon="sync">sync</span>
                            </div>
                            <span
                                class="bg-tertiary-fixed text-on-tertiary-container text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-tighter">85%
                                SLA</span>
                        </div>
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Đang giải quyết</p>
                        <h3 class="text-3xl font-extrabold text-zinc-900">{{ $processingTickets }}</h3>
                    </div>
                    <div
                        class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-zinc-100 hover:border-zinc-400 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 bg-zinc-50 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-zinc-500" data-icon="task_alt">task_alt</span>
                            </div>
                            <span
                                class="bg-zinc-100 text-zinc-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-tighter">92%
                                CSAT</span>
                        </div>
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-1">Đã hoàn thành</p>
                        <h3 class="text-3xl font-extrabold text-zinc-900">{{ $doneTickets }}</h3>
                    </div>
                </div>
                <!-- Filters Section -->
                <div class="bg-white p-4 rounded-xl shadow-sm mb-6 flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[240px] relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-sm">search</span>
                        <input
                            class="w-full bg-surface-container border-none focus:ring-2 focus:ring-amber-500/20 rounded-lg pl-9 pr-4 py-2 text-sm"
                            placeholder="ID vé, Tên khách hàng..." type="text" />
                    </div>
                    <div class="w-48">
                        <select
                            class="w-full bg-surface-container border-none focus:ring-2 focus:ring-amber-500/20 rounded-lg px-4 py-2 text-sm text-zinc-600 appearance-none cursor-pointer">
                            <option>Trạng thái</option>
                            <option>Mới</option>
                            <option>Đang xử lý</option>
                            <option>Chờ phản hồi</option>
                            <option>Hoàn thành</option>
                        </select>
                    </div>
                    <div class="w-40">
                        <select
                            class="w-full bg-surface-container border-none focus:ring-2 focus:ring-amber-500/20 rounded-lg px-4 py-2 text-sm text-zinc-600 cursor-pointer">
                            <option>Mức ưu tiên</option>
                            <option>Cao</option>
                            <option>Trung bình</option>
                            <option>Thấp</option>
                        </select>
                    </div>
                    <div class="w-48">
                        <select
                            class="w-full bg-surface-container border-none focus:ring-2 focus:ring-amber-500/20 rounded-lg px-4 py-2 text-sm text-zinc-600 cursor-pointer">
                            <option>Nhân viên xử lý</option>
                            <option>Admin 01</option>
                            <option>Admin 02</option>
                        </select>
                    </div>
                    <button
                        class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">filter_alt</span>
                        Lọc
                    </button>
                    <button class="text-zinc-400 hover:text-amber-500 transition-colors">
                        <span class="material-symbols-outlined">restart_alt</span>
                    </button>
                </div>
                <!-- Data Table Area -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low border-b border-zinc-100">
                                    <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">ID Vé
                                    </th>
                                    <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Khách
                                        hàng</th>
                                    <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Tiêu đề
                                    </th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest text-center">
                                        Ưu tiên</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest text-center">
                                        Trạng thái</th>
                                    <th class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest">Ngày
                                        tạo</th>
                                    <th
                                        class="px-6 py-4 text-xs font-bold text-zinc-500 uppercase tracking-widest text-right">
                                        Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-50">
                                @foreach ($tickets as $ticket)
                                    <!-- Ticket 1 -->
                                    <tr class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-sm text-zinc-900">{{ $ticket->ticket_code }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img alt="Nguyễn Linh" class="w-8 h-8 rounded-full border border-zinc-100"
                                                    data-alt="professional portrait of a young Vietnamese woman with long dark hair, friendly expression, soft office lighting"
                                                    src="https://ui-avatars.com/api/?name={{ $ticket->customer_name }}" />
                                                <span
                                                    class="text-sm font-medium text-zinc-700">{{ $ticket->customer_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-zinc-900">{{ $ticket->title }}</p>
                                            <p class="text-[11px] text-zinc-500 truncate max-w-[200px]">
                                                {{ Str::limit($ticket->description, 50) }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($ticket->priority == 'high')
                                                <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs">Cao</span>
                                            @elseif($ticket->priority == 'medium')
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded text-xs">Trung
                                                    bình</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">Thấp</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($ticket->status == 'new')
                                                <span
                                                    class="px-2 py-1 bg-green-100 text-green-600 rounded text-xs">Mới</span>
                                            @elseif($ticket->status == 'processing')
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded text-xs">Đang
                                                    xử lý</span>
                                            @elseif($ticket->status == 'waiting')
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">Chờ phản
                                                    hồi</span>
                                            @else
                                                <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded text-xs">Hoàn
                                                    thành</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-zinc-500">
                                            {{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.tickets.show', $ticket->id) }}">
                                                    <button
                                                        class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-amber-100 text-amber-600 transition-all"><span
                                                            class="material-symbols-outlined text-lg">visibility</span>
                                                    </button>
                                                </a>
                                                <button
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-zinc-100 text-zinc-600 transition-all"><span
                                                        class="material-symbols-outlined text-lg">reply</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Shell -->
                    <div class="px-6 py-4 border-t border-zinc-100 flex items-center justify-between">

                        <p class="text-xs text-zinc-500 font-medium">
                            Hiển thị
                            <span class="text-zinc-900 font-bold">{{ $tickets->firstItem() }}</span>
                            -
                            <span class="text-zinc-900 font-bold">{{ $tickets->lastItem() }}</span>
                            trong số
                            <span class="text-zinc-900 font-bold">{{ $tickets->total() }}</span>
                            kết quả
                        </p>

                        <div class="flex items-center gap-2">

                            {{-- Previous --}}
                            @if ($tickets->onFirstPage())
                                <button
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-zinc-200 text-zinc-400 opacity-50">

                                    <span class="material-symbols-outlined text-sm">
                                        chevron_left
                                    </span>

                                </button>
                            @else
                                <a href="{{ $tickets->previousPageUrl() }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50">

                                    <span class="material-symbols-outlined text-sm">
                                        chevron_left
                                    </span>

                                </a>
                            @endif


                            {{-- Page Numbers --}}
                            @foreach ($tickets->getUrlRange(1, $tickets->lastPage()) as $page => $url)
                                @if ($page == $tickets->currentPage())
                                    <span
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-on-primary-fixed font-bold text-xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-zinc-50 text-zinc-600 font-bold text-xs">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach


                            {{-- Next --}}
                            @if ($tickets->hasMorePages())
                                <a href="{{ $tickets->nextPageUrl() }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50">

                                    <span class="material-symbols-outlined text-sm">
                                        chevron_right
                                    </span>

                                </a>
                            @else
                                <button
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-zinc-200 text-zinc-400 opacity-50">

                                    <span class="material-symbols-outlined text-sm">
                                        chevron_right
                                    </span>

                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>

    </html>
@endsection
