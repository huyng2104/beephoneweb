<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Bee Phone - Chuyên gia Công nghệ')</title>
    
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bee-yellow': '#FFD700',
                        'bee-dark': '#1A1A1A',
                        'bee-gray-light': '#F8F9FA',
                        'bee-gray-border': '#E9ECEF',
                    },
                    fontFamily: {
                        sans: ['Be Vietnam Pro', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style data-purpose="custom-shadows">
        .card-shadow { box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; }
        .card-shadow:hover { box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1); transform: translateY(-4px); }
        .smooth-transition { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans antialiased">

    @include('client.layouts.header')

    <main>
        @yield('content')
    </main>

    @include('client.layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</body>
</html>