<!DOCTYPE html>

<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&amp;display=swap"
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
                        "primary": "#f4c025",
                        "background-light": "#f8f6f6",
                        "background-dark": "#221610",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
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
    <title>Quên mật khẩu - Bee Phone</title>
</head>

<body
    class="bg-background-light dark:bg-background-dark font-display min-h-screen flex items-center justify-center p-4">
    <div
        class="w-full max-w-[480px] bg-white dark:bg-slate-900 shadow-xl rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800">
        <!-- Header / Logo Section -->
        <div class="pt-10 pb-6 flex flex-col items-center">
            <div class="bg-primary/10 p-4 rounded-full mb-4">
                <span class="material-symbols-outlined text-primary text-4xl">
                    lock_reset
                </span>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <div class="size-6 text-primary">
                    <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd"
                            d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z"
                            fill="currentColor" fill-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-slate-900 dark:text-slate-100 text-xl font-bold leading-tight tracking-[-0.015em]">Bee
                    Phone</h2>
            </div>
        </div>
        <!-- Content Section -->
        <div class="px-8 pb-10">
            <h1
                class="text-slate-900 dark:text-slate-100 tracking-tight text-2xl font-bold leading-tight text-center pb-2">
                Quên mật khẩu?
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-base font-normal leading-relaxed text-center pb-8">
                Đừng lo lắng, hãy nhập email của bạn và chúng tôi sẽ gửi hướng dẫn khôi phục mật khẩu cho bạn.
            </p>
            <form action="{{route('post-reset-password')}}" method="post" class="flex flex-col gap-6">
                @csrf
                <div class="flex flex-col gap-2">
                    <label class="text-slate-900 dark:text-slate-100 text-sm font-semibold leading-normal">
                        Địa chỉ Email
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            mail
                        </span>
                        <input name="email" value="{{old('email')}}"
                            class="form-input flex w-full rounded-xl text-slate-900 dark:text-slate-100 focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 h-14 pl-12 pr-4 placeholder:text-slate-400 text-base font-normal leading-normal transition-all"
                            placeholder="example@gmail.com" required="" type="text" />
                    </div>
                </div>
                @error('email')
                    <span class="text-red-500"> {{$message}} </span>
                @enderror
                <button
                    class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-xl h-14 px-5 bg-primary text-slate-900 text-base font-bold leading-normal tracking-[0.015em] hover:brightness-105 active:scale-[0.98] transition-all shadow-lg shadow-primary/20"
                    type="submit">
                    <span class="truncate">Gửi yêu cầu</span>
                </button>
            </form>
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                <a class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-primary dark:hover:text-primary transition-colors text-sm font-medium"
                    href="{{route('login')}}">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>
</body>

</html>
