<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'Bee Phone')</title>


    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap"
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
                        "primary": "#f4c025",
                        "background-light": "#f8f8f5",
                        "background-dark": "#221e10",
                    },
                    fontFamily: {
                        "display": ["Space Grotesk", "sans-serif"]
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


    <style type="text/tailwindcss">
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: "Space Grotesk", sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .ai-sparkle {
            background: linear-gradient(90deg, #f4c025, #fff, #f4c025);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }
    </style>


</head>

<body class="bg-background-light dark:bg-background-dark text-[#181611] dark:text-white transition-colors duration-200">
    {{-- Header --}}
    @include('client.layouts.header')


    {{-- Content --}}
    @yield('content')

    {{-- Chat box --}}
    <div class="fixed bottom-6 right-6 z-[60] flex flex-col items-end gap-3 pointer-events-none">
        <div
            class="bg-white dark:bg-[#181611] p-4 rounded-2xl shadow-2xl border border-primary/20 max-w-[240px] pointer-events-auto transform translate-y-2 opacity-0 animate-[fade-in-up_0.5s_ease-out_forwards] delay-500">
            <p class="text-sm font-medium leading-relaxed">Chào bạn! Tôi có thể giúp bạn chọn chiếc điện thoại phù hợp
                không?</p>
        </div>
        <button
            class="size-14 bg-primary rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-transform pointer-events-auto group relative">
            <span class="material-symbols-outlined text-black text-3xl">smart_toy</span>
            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-primary border-2 border-white"></span>
            </span>
        </button>
    </div>


    {{-- Footer --}}
    @include('client.layouts.footer')




    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

</body>

</html>
