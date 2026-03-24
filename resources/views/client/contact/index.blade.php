@extends('client.layouts.app')

@section('title', 'Liên hệ và Hỗ trợ Bee Phone')

@section('content')
<div class="bg-background-light dark:bg-background-dark text-[#181611] dark:text-white font-display min-h-screen">
    <div class="max-w-[1200px] mx-auto px-4 lg:px-10 py-10">
        <!-- Page Heading -->
        <div class="mb-10 text-center max-w-2xl mx-auto">
            <h1 class="text-[#181611] dark:text-white text-4xl lg:text-5xl font-black leading-tight tracking-[-0.033em] mb-4">
                Chúng tôi có thể giúp gì cho bạn?
            </h1>
            <p class="text-[#8a8060] dark:text-white/60 text-lg font-normal leading-normal">
                Tìm kiếm câu trả lời nhanh chóng hoặc gửi yêu cầu hỗ trợ trực tiếp cho đội ngũ Bee Phone
            </p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-3xl mx-auto mb-16">
            <div class="px-4 py-3">
                <label class="flex flex-col min-w-40 h-14 w-full shadow-lg rounded-xl overflow-hidden">
                    <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
                        <div class="text-[#8a8060] flex border-none bg-white dark:bg-white/5 items-center justify-center pl-5 border-r-0">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden text-[#181611] dark:text-white focus:outline-0 focus:ring-0 border-none bg-white dark:bg-white/5 h-full placeholder:text-[#8a8060] px-4 pl-3 text-lg font-normal leading-normal" placeholder="Tìm kiếm câu hỏi thường gặp..." value="" />
                    </div>
                </label>
            </div>
        </div>

        <!-- FAQ Section -->
        <section class="mb-20">
            <div class="flex items-center justify-center gap-2 mb-8">
                <span class="material-symbols-outlined text-primary">help_center</span>
                <h2 class="text-[#181611] dark:text-white text-2xl font-bold leading-tight tracking-[-0.015em]">Câu hỏi thường gặp (FAQ)</h2>
            </div>
            <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-4">
                <details class="flex flex-col rounded-xl border border-[#e6e3db] dark:border-white/10 bg-white dark:bg-white/5 px-5 py-4 group cursor-pointer hover:border-primary/50 transition-colors" open>
                    <summary class="flex items-center justify-between gap-6 py-1 list-none">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">local_shipping</span>
                            <p class="text-[#181611] dark:text-white text-base font-semibold leading-normal">Giao hàng</p>
                        </div>
                        <span class="material-symbols-outlined text-[#181611] dark:text-white group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-[#8a8060] dark:text-white/60 text-sm font-normal leading-relaxed pt-4">
                        Bee Phone hỗ trợ giao hàng hỏa tốc trong 2h tại Hà Nội và TP.HCM. Đối với các tỉnh thành khác, thời gian nhận hàng từ 2-4 ngày làm việc. Chúng tôi miễn phí vận chuyển cho đơn hàng trên 2 triệu đồng.
                    </p>
                </details>
                <details class="flex flex-col rounded-xl border border-[#e6e3db] dark:border-white/10 bg-white dark:bg-white/5 px-5 py-4 group cursor-pointer hover:border-primary/50 transition-colors">
                    <summary class="flex items-center justify-between gap-6 py-1 list-none">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">assignment_return</span>
                            <p class="text-[#181611] dark:text-white text-base font-semibold leading-normal">Đổi trả</p>
                        </div>
                        <span class="material-symbols-outlined text-[#181611] dark:text-white group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-[#8a8060] dark:text-white/60 text-sm font-normal leading-relaxed pt-4">
                        Quý khách có thể đổi trả sản phẩm trong vòng 30 ngày nếu phát sinh lỗi từ nhà sản xuất. Sản phẩm phải còn nguyên hộp, phụ kiện và chưa qua sửa chữa bên ngoài.
                    </p>
                </details>
                <details class="flex flex-col rounded-xl border border-[#e6e3db] dark:border-white/10 bg-white dark:bg-white/5 px-5 py-4 group cursor-pointer hover:border-primary/50 transition-colors">
                    <summary class="flex items-center justify-between gap-6 py-1 list-none">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">verified_user</span>
                            <p class="text-[#181611] dark:text-white text-base font-semibold leading-normal">Bảo hành</p>
                        </div>
                        <span class="material-symbols-outlined text-[#181611] dark:text-white group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-[#8a8060] dark:text-white/60 text-sm font-normal leading-relaxed pt-4">
                        Mọi sản phẩm tại Bee Phone đều được hưởng chính sách bảo hành chính hãng 12 tháng. Bạn có thể tra cứu bảo hành điện tử qua số IMEI/Serial Number ngay trên website.
                    </p>
                </details>
                <details class="flex flex-col rounded-xl border border-[#e6e3db] dark:border-white/10 bg-white dark:bg-white/5 px-5 py-4 group cursor-pointer hover:border-primary/50 transition-colors">
                    <summary class="flex items-center justify-between gap-6 py-1 list-none">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">payments</span>
                            <p class="text-[#181611] dark:text-white text-base font-semibold leading-normal">Thanh toán</p>
                        </div>
                        <span class="material-symbols-outlined text-[#181611] dark:text-white group-open:rotate-180 transition-transform">expand_more</span>
                    </summary>
                    <p class="text-[#8a8060] dark:text-white/60 text-sm font-normal leading-relaxed pt-4">
                        Chúng tôi chấp nhận thanh toán qua thẻ VISA/MasterCard, chuyển khoản, ví MoMo, ZaloPay và hỗ trợ trả góp 0% lãi suất qua thẻ tín dụng hoặc các công ty tài chính.
                    </p>
                </details>
            </div>
        </section>

        <!-- Contact Support Form and Info -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">
            <div class="lg:col-span-3 bg-white dark:bg-white/5 rounded-2xl border border-[#e6e3db] dark:border-white/10 p-8 shadow-sm">
                <h3 class="text-2xl font-bold mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">mail</span>
                    Gửi yêu cầu hỗ trợ
                </h3>
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold">Họ và tên</label>
                            <input name="name" value="{{ old('name') }}" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" placeholder="Nhập họ tên của bạn" type="text" required />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold">Email</label>
                            <input name="email" value="{{ old('email') }}" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" placeholder="example@email.com" type="email" required />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold">Số điện thoại</label>
                            <input name="phone" value="{{ old('phone') }}" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" placeholder="0xxx xxx xxx" type="tel" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-semibold">Chủ đề cần hỗ trợ</label>
                            <select name="category" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3">
                                <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>Chung</option>
                                <option value="payment" {{ old('category') == 'payment' ? 'selected' : '' }}>Thanh toán</option>
                                <option value="warranty" {{ old('category') == 'warranty' ? 'selected' : '' }}>Bảo hành</option>
                                <option value="shipping" {{ old('category') == 'shipping' ? 'selected' : '' }}>Vận chuyển</option>
                                <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Kỹ thuật</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold">Nội dung lời nhắn</label>
                        <textarea name="message" rows="5" class="rounded-lg border-[#e6e3db] dark:border-white/10 bg-background-light dark:bg-white/5 focus:border-primary focus:ring-primary w-full px-4 py-3" placeholder="Bạn cần chúng tôi hỗ trợ gì?" required>{{ old('message') }}</textarea>
                    </div>
                    <button class="w-full md:w-auto min-w-[200px] h-14 bg-primary text-[#181611] font-bold text-lg rounded-xl hover:opacity-90 transition-all shadow-md shadow-primary/20 flex items-center justify-center gap-2" type="submit">
                        <span class="material-symbols-outlined">send</span>
                        Gửi yêu cầu
                    </button>
                </form>
            </div>
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e6e3db] dark:border-white/10 p-8 shadow-sm">
                    <h3 class="text-xl font-bold mb-6">Liên hệ trực tiếp</h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-full bg-primary/20 text-primary">
                                <span class="material-symbols-outlined">call</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase text-[#8a8060] dark:text-white/40 tracking-wider">Hotline 24/7</p>
                                <p class="text-lg font-bold">1900 8888</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-full bg-primary/20 text-primary">
                                <span class="material-symbols-outlined">alternate_email</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase text-[#8a8060] dark:text-white/40 tracking-wider">Email hỗ trợ</p>
                                <p class="text-lg font-bold">support@beephone.vn</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="p-3 rounded-full bg-primary/20 text-primary">
                                <span class="material-symbols-outlined">location_on</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase text-[#8a8060] dark:text-white/40 tracking-wider">Địa chỉ cửa hàng</p>
                                <p class="text-base font-medium">123 Đường Láng, Quận Đống Đa, Hà Nội</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[#e6e3db] dark:bg-white/10 rounded-2xl h-64 w-full overflow-hidden relative group">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDAdd2UHS0Gr0pyQyQ1icq1S6EaTjSiZqkkarE8LHKx5l3YZB25roFrllb49cEkJAGR7WmrJsxqAPE5f7fQENPjO4I-NEpgaSrYwSXuyOwxV-PMDPdxzGR78w8vjTP8Ig8vCAUmGn8DyQa4NCMCoqmh3ykKljG98GxWbAjo3lWYhlPoHasGG5mo3qobyKr42TQRNiYGphVj0meFPNG6VEcwRfbf8Z0UYMm3yNFC0TazqWzMOltEwFMRXjbmTdYYPy_JDRg_Kkkb5GI');"></div>
                    <div class="absolute inset-0 bg-black/10 flex items-center justify-center">
                        <div class="bg-white dark:bg-background-dark px-4 py-2 rounded-lg shadow-xl flex items-center gap-2">
                            <span class="material-symbols-outlined text-red-500">location_on</span>
                            <span class="text-sm font-bold">Bee Phone Hà Nội</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chatbot Modal -->
        <div id="chatbot-modal" class="fixed inset-0 z-[100] hidden bg-black/50 flex items-center justify-center">
            <div class="bg-white dark:bg-background-dark rounded-2xl shadow-2xl w-full max-w-md mx-4 h-[600px] flex flex-col">
                <!-- Header -->
                <div class="bg-primary text-[#181611] p-4 rounded-t-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#181611]/20 rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined">smart_toy</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Bee Phone Assistant</h3>
                            <p class="text-sm opacity-90">Hỗ trợ 24/7</p>
                        </div>
                    </div>
                    <button id="chat-close" class="text-[#181611] hover:bg-[#181611]/10 rounded-full p-2">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Messages -->
                <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-sm text-[#181611]">smart_toy</span>
                        </div>
                        <div class="bg-gray-100 dark:bg-white/10 rounded-2xl rounded-tl-md p-3 max-w-[80%]">
                            <p class="text-sm">Xin chào! Tôi có thể giúp gì cho bạn? Hãy chọn chủ đề bạn quan tâm:</p>
                        </div>
                    </div>
                </div>

                <!-- Category Selection -->
                <div id="category-selection" class="p-4 border-t border-gray-200 dark:border-white/10">
                    <p class="text-sm font-medium mb-3 text-[#181611] dark:text-white">Chọn chủ đề:</p>
                    <div id="category-buttons" class="grid grid-cols-2 gap-2">
                        <!-- Categories will be loaded here -->
                    </div>
                </div>

                <!-- Questions List (Hidden initially) -->
                <div id="questions-section" class="hidden p-4 border-t border-gray-200 dark:border-white/10 max-h-48 overflow-y-auto">
                    <p class="text-sm font-medium mb-3 text-[#181611] dark:text-white">Các câu hỏi thường gặp:</p>
                    <div id="questions-list" class="space-y-2">
                        <!-- Questions will be loaded here -->
                    </div>
                </div>

                <!-- Contact Support Button -->
                <div class="p-4 border-t border-gray-200 dark:border-white/10">
                    <button id="contact-support-btn" class="w-full bg-primary text-[#181611] font-bold py-3 px-4 rounded-xl hover:opacity-90 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">person</span>
                        Gặp nhân viên hỗ trợ
                    </button>
                </div>
            </div>
        </div>

        <footer class="bg-white dark:bg-white/5 border-t border-[#f5f3f0] dark:border-white/10 mt-20 pb-10">
            <div class="max-w-[1200px] mx-auto px-4 lg:px-10 py-8 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-4">
                    <div class="size-5 text-primary">
                        <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z" fill="currentColor" fill-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-[#181611] dark:text-white text-base font-bold">Bee Phone</h2>
                </div>
                <p class="text-[#8a8060] dark:text-white/40 text-sm">© 2024 Bee Phone Store. All rights reserved.</p>
                <div class="flex gap-6">
                    <a class="text-[#8a8060] dark:text-white/40 hover:text-primary transition-colors" href="#">Privacy Policy</a>
                    <a class="text-[#8a8060] dark:text-white/40 hover:text-primary transition-colors" href="#">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatbotModal = document.getElementById('chatbot-modal');
    const chatClose = document.getElementById('chat-close');
    const categoryButtons = document.getElementById('category-buttons');
    const questionsSection = document.getElementById('questions-section');
    const questionsList = document.getElementById('questions-list');
    const contactSupportBtn = document.getElementById('contact-support-btn');

    // Toggle chatbot modal
    chatToggle.addEventListener('click', () => {
        chatbotModal.classList.remove('hidden');
        loadCategories();
    });

    chatClose.addEventListener('click', () => {
        chatbotModal.classList.add('hidden');
        resetChatbot();
    });

    // Close modal when clicking outside
    chatbotModal.addEventListener('click', (e) => {
        if (e.target === chatbotModal) {
            chatbotModal.classList.add('hidden');
            resetChatbot();
        }
    });

    // Load categories from API
    function loadCategories() {
        fetch('/api/chatbot/categories')
            .then(response => response.json())
            .then(categories => {
                categoryButtons.innerHTML = '';
                categories.forEach(category => {
                    const button = document.createElement('button');
                    button.className = 'p-3 bg-gray-100 dark:bg-white/10 hover:bg-primary hover:text-[#181611] rounded-lg text-sm font-medium transition-colors text-left';
                    button.textContent = category.name;
                    button.addEventListener('click', () => selectCategory(category.key));
                    categoryButtons.appendChild(button);
                });
            })
            .catch(error => {
                console.error('Error loading categories:', error);
                showError('Không thể tải danh mục. Vui lòng thử lại.');
            });
    }

    // Select category and load questions
    function selectCategory(categoryKey) {
        fetch(`/api/chatbot/questions/${categoryKey}`)
            .then(response => response.json())
            .then(questions => {
                questionsList.innerHTML = '';
                Object.keys(questions).forEach(question => {
                    const questionDiv = document.createElement('div');
                    questionDiv.className = 'border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden';

                    const questionBtn = document.createElement('button');
                    questionBtn.className = 'w-full p-3 text-left hover:bg-gray-50 dark:hover:bg-white/5 transition-colors flex items-center justify-between';
                    questionBtn.innerHTML = `
                        <span class="text-sm font-medium">${question}</span>
                        <span class="material-symbols-outlined text-gray-400">expand_more</span>
                    `;

                    const answerDiv = document.createElement('div');
                    answerDiv.className = 'hidden p-3 bg-gray-50 dark:bg-white/5 text-sm border-t border-gray-200 dark:border-white/10';
                    answerDiv.textContent = questions[question];

                    questionBtn.addEventListener('click', () => {
                        answerDiv.classList.toggle('hidden');
                        const icon = questionBtn.querySelector('.material-symbols-outlined');
                        icon.textContent = answerDiv.classList.contains('hidden') ? 'expand_more' : 'expand_less';
                    });

                    questionDiv.appendChild(questionBtn);
                    questionDiv.appendChild(answerDiv);
                    questionsList.appendChild(questionDiv);
                });

                // Hide category selection and show questions
                document.getElementById('category-selection').classList.add('hidden');
                questionsSection.classList.remove('hidden');

                // Add back button
                addBackButton();
            })
            .catch(error => {
                console.error('Error loading questions:', error);
                showError('Không thể tải câu hỏi. Vui lòng thử lại.');
            });
    }

    // Add back button to return to categories
    function addBackButton() {
        const backButton = document.createElement('button');
        backButton.className = 'w-full mb-3 p-2 bg-gray-200 dark:bg-white/20 hover:bg-gray-300 dark:hover:bg-white/30 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2';
        backButton.innerHTML = '<span class="material-symbols-outlined text-sm">arrow_back</span> Quay lại chọn chủ đề';
        backButton.addEventListener('click', () => {
            questionsSection.classList.add('hidden');
            document.getElementById('category-selection').classList.remove('hidden');
            // Remove back button
            backButton.remove();
        });

        questionsSection.insertBefore(backButton, questionsSection.firstChild);
    }

    // Contact support button
    contactSupportBtn.addEventListener('click', () => {
        chatbotModal.classList.add('hidden');
        resetChatbot();
        // Scroll to contact form
        document.querySelector('.bg-white.dark\\:bg-white\\/5.rounded-2xl').scrollIntoView({ behavior: 'smooth' });
    });

    // Reset chatbot state
    function resetChatbot() {
        questionsSection.classList.add('hidden');
        document.getElementById('category-selection').classList.remove('hidden');
        questionsList.innerHTML = '';
    }

    // Show error message
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'p-3 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-sm';
        errorDiv.textContent = message;

        const messages = document.getElementById('chat-messages');
        messages.appendChild(errorDiv);

        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
});
</script>

@endsection
