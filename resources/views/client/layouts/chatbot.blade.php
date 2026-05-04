<div id="chat-bubble" class="fixed bottom-6 right-6 bg-[#f4c025] p-4 rounded-full cursor-pointer shadow-xl hover:scale-110 transition-transform z-50 flex items-center justify-center group">
    <span class="material-symbols-outlined text-[#181611] text-3xl">smart_toy</span>
    <span id="notification-dot" class="absolute -top-1 -right-1 flex h-4 w-4">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
        <span class="notification-dot-inner relative inline-flex rounded-full h-4 w-4 bg-white border-2 border-[#f4c025]"></span>
    </span>
</div>

<div id="chat-window" class="fixed bottom-24 right-6 w-[400px] h-[550px] max-h-[calc(100vh-120px)] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800 flex-col hidden z-50 overflow-hidden flex">
    <!-- Header with Tabs -->
    <div class="bg-[#181611] dark:bg-black text-white p-0 flex flex-col">
        <div class="flex justify-between items-center p-3 pb-0">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#f4c025]">support_agent</span>
                <h3 class="font-bold text-sm">Hỗ Trợ Trực Tuyến</h3>
            </div>
            <button id="close-chat" class="text-gray-400 hover:text-white"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="flex px-3 mt-2 border-b border-gray-700">
            <button id="tab-ai" class="flex-1 pb-2 text-sm font-bold text-[#f4c025] border-b-2 border-[#f4c025] transition-colors">AI Tư Vấn</button>
            <button id="tab-staff" class="flex-1 pb-2 text-sm font-bold text-gray-400 border-b-2 border-transparent hover:text-gray-200 transition-colors">Nhân Viên</button>
        </div>
    </div>

    <!-- SECTION AI -->
    <div id="section-ai" class="flex-1 flex flex-col bg-gray-50 dark:bg-slate-800 min-h-0">
        <div id="chat-box-ai" class="flex-1 p-4 overflow-y-auto flex flex-col gap-3 text-sm min-h-0">
            <div class="flex items-start gap-2">
                <div class="w-8 h-8 rounded-full bg-[#f4c025] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[#181611] text-sm">smart_toy</span>
                </div>
                <div class="bg-white dark:bg-slate-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-slate-800 dark:text-white max-w-[80%]">
                    Dạ em chào anh/chị! Em là trợ lý AI của BeePhone. Anh/chị đang cần hỗ trợ về sản phẩm hay dịch vụ nào ạ?
                </div>
            </div>
        </div>
        <div class="p-3 bg-white dark:bg-slate-900 border-t border-gray-100 dark:border-slate-800 flex items-center gap-2">
            <input type="text" id="chat-input-ai" class="flex-1 w-full bg-gray-100 dark:bg-slate-800 border-none rounded-full px-4 py-2 text-sm focus:ring-0 dark:text-white outline-none" placeholder="Hỏi AI...">
            <button id="send-chat-ai" class="w-10 h-10 shrink-0 bg-[#f4c025] rounded-full flex items-center justify-center text-[#181611] hover:brightness-105 transition-all">
                <span class="material-symbols-outlined text-sm">send</span>
            </button>
        </div>
    </div>

    <!-- SECTION STAFF -->
    <div id="section-staff" class="flex-1 flex flex-col bg-gray-50 dark:bg-slate-800 min-h-0 hidden">
        <div id="chat-box-staff" class="flex-1 p-4 overflow-y-auto flex flex-col gap-3 text-sm relative min-h-0">
            <div id="staff-connect-prompt" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 dark:bg-slate-800 z-10">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-2xl">support_agent</span>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-300 mb-4 text-center px-4">Kết nối với nhân viên để được hỗ trợ chi tiết hơn.</p>
                <button id="contact-staff-btn-new" class="bg-blue-500 text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-blue-600 transition-colors shadow-md">Kết Nối Ngay</button>
            </div>
            <div id="staff-chat-content" class="flex flex-col gap-3 w-full"></div>
        </div>
        <div class="p-3 bg-white dark:bg-slate-900 border-t border-gray-100 dark:border-slate-800 flex items-center gap-2">
            <input type="text" id="chat-input-staff" class="flex-1 w-full bg-gray-100 dark:bg-slate-800 border-none rounded-full px-4 py-2 text-sm focus:ring-0 dark:text-white outline-none disabled:opacity-50" placeholder="Nhắn cho nhân viên..." disabled>
            <button id="send-chat-staff" class="w-10 h-10 shrink-0 bg-blue-500 rounded-full flex items-center justify-center text-white hover:brightness-105 transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <span class="material-symbols-outlined text-sm">send</span>
            </button>
            <button id="end-staff-chat-btn" class="w-10 h-10 shrink-0 bg-red-100 text-red-500 rounded-full flex items-center justify-center hover:bg-red-200 transition-all hidden" title="Kết thúc chat">
                <span class="material-symbols-outlined text-sm">stop_circle</span>
            </button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Hàm cập nhật chấm thông báo sang đỏ
    function updateNotificationDotToRed() {
        const dotInner = document.querySelector('.notification-dot-inner');
        const dotOuter = document.querySelector('#notification-dot span:first-child');
        if (dotInner) {
            dotInner.classList.remove('bg-white');
            dotInner.classList.add('bg-red-500');
            dotInner.style.borderColor = '#f4c025';
        }
        if (dotOuter) {
            dotOuter.classList.remove('bg-white');
            dotOuter.classList.add('bg-red-500');
        }
    }

    // Hàm reset chấm thông báo về trắng
    function resetNotificationDotToWhite() {
        const dotInner = document.querySelector('.notification-dot-inner');
        const dotOuter = document.querySelector('#notification-dot span:first-child');
        if (dotInner) {
            dotInner.classList.remove('bg-red-500');
            dotInner.classList.add('bg-white');
        }
        if (dotOuter) {
            dotOuter.classList.remove('bg-red-500');
            dotOuter.classList.add('bg-white');
        }
    }

    // Vanilla JS: Click to open/close chat
    document.addEventListener('DOMContentLoaded', function() {
        const chatBubble = document.getElementById('chat-bubble');
        const chatWindow = document.getElementById('chat-window');
        const closeBtn = document.getElementById('close-chat');
        
        if (chatBubble) {
            chatBubble.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (chatWindow) {
                    chatWindow.classList.remove('hidden');
                    chatWindow.classList.add('flex');
                    chatBubble.classList.add('hidden');
                    sessionStorage.setItem('beephone_chat_state', 'open');
                    resetNotificationDotToWhite();
                }
            });
        }

        if (closeBtn && chatWindow) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                chatWindow.classList.add('hidden');
                chatWindow.classList.remove('flex');
                chatBubble.classList.remove('hidden');
                sessionStorage.setItem('beephone_chat_state', 'closed');
            });
        }

        // Restore state from session
        if (sessionStorage.getItem('beephone_chat_state') === 'open' && chatWindow && chatBubble) {
            chatWindow.classList.remove('hidden');
            chatWindow.classList.add('flex');
            chatBubble.classList.add('hidden');
            resetNotificationDotToWhite();
        }
    });

    // jQuery Chat Functionality
    $(document).ready(function() {
        const CHAT_TICKET_ID_KEY = 'beephone_chat_ticket_id';
        const CHAT_LAST_MESSAGE_ID_KEY = 'beephone_chat_last_message_id';
        const ACTIVE_TAB_KEY = 'beephone_active_tab';
        let pollingTimer = null;

        // Tabs Logic
        function switchTab(tab) {
            if (tab === 'staff') {
                $('#tab-staff').removeClass('text-gray-400 border-transparent').addClass('text-blue-500 border-blue-500');
                $('#tab-ai').removeClass('text-[#f4c025] border-[#f4c025]').addClass('text-gray-400 border-transparent');
                $('#section-staff').removeClass('hidden').addClass('flex');
                $('#section-ai').removeClass('flex').addClass('hidden');
                sessionStorage.setItem(ACTIVE_TAB_KEY, 'staff');
                scrollToBottomStaff();
            } else {
                $('#tab-ai').removeClass('text-gray-400 border-transparent').addClass('text-[#f4c025] border-[#f4c025]');
                $('#tab-staff').removeClass('text-blue-500 border-blue-500').addClass('text-gray-400 border-transparent');
                $('#section-ai').removeClass('hidden').addClass('flex');
                $('#section-staff').removeClass('flex').addClass('hidden');
                sessionStorage.setItem(ACTIVE_TAB_KEY, 'ai');
                scrollToBottomAi();
            }
        }

        $('#tab-ai').click(() => switchTab('ai'));
        $('#tab-staff').click(() => switchTab('staff'));

        if (sessionStorage.getItem(ACTIVE_TAB_KEY) === 'staff') {
            switchTab('staff');
        } else {
            switchTab('ai'); // default
        }

        function saveChatHistoryAi() {
            sessionStorage.setItem('beephone_chat_history_ai', $('#chat-box-ai').html());
        }
        function saveChatHistoryStaff() {
            sessionStorage.setItem('beephone_chat_history_staff', $('#staff-chat-content').html());
        }

        function scrollToBottomAi() {
            const chatBox = document.getElementById('chat-box-ai');
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        }
        function scrollToBottomStaff() {
            const chatBox = document.getElementById('chat-box-staff');
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
        }

        function escapeHtml(text) { return $('<div>').text(text ?? '').html(); }
        function getTicketId() { return sessionStorage.getItem(CHAT_TICKET_ID_KEY); }
        function setTicketId(ticketId) { if (ticketId) sessionStorage.setItem(CHAT_TICKET_ID_KEY, String(ticketId)); }
        function getLastMessageId() { const id = sessionStorage.getItem(CHAT_LAST_MESSAGE_ID_KEY); return id ? Number(id) : 0; }
        function setLastMessageId(id) { if (id) sessionStorage.setItem(CHAT_LAST_MESSAGE_ID_KEY, String(id)); }
        function getCustomerName() { return @json(optional(auth()->user())->name ?? 'Khách hàng'); }
        function formatPriceVnd(value) { const n = Number(value || 0); return `${n.toLocaleString('vi-VN')}đ`; }

        function renderProductList(title, items) {
            if (!Array.isArray(items) || items.length === 0) return '';
            const cards = items.slice(0, 3).map(function(item) {
                const thumbStyle = item.thumbnail ? `style="background-image:url('${escapeHtml(item.thumbnail)}')"` : '';
                const url = item.url || '#';
                return `
                    <a href="${escapeHtml(url)}" class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                        <div class="w-10 h-10 rounded bg-slate-100 dark:bg-slate-600 bg-cover bg-center shrink-0" ${thumbStyle}></div>
                        <div class="min-w-0">
                            <p class="text-xs text-slate-800 dark:text-white line-clamp-2">${escapeHtml(item.name || 'Sản phẩm')}</p>
                            <p class="text-xs font-bold text-red-500">${formatPriceVnd(item.price)}</p>
                        </div>
                    </a>
                `;
            }).join('');
            return `
                <div class="ml-10 mt-2">
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">${escapeHtml(title)}</p>
                    <div class="grid grid-cols-1 gap-1.5">${cards}</div>
                </div>
            `;
        }

        function appendAdminReply(senderName, message, createdAt) {
            $('#staff-chat-content').append(`
                <div class="flex items-start gap-2">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">support_agent</span>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-2xl rounded-tl-none shadow-sm text-blue-900 dark:text-blue-100 max-w-[80%]">
                        <p class="text-xs font-semibold opacity-80 mb-1">${escapeHtml(senderName || 'Nhân viên hỗ trợ')} • ${escapeHtml(createdAt || '')}</p>
                        <p>${escapeHtml(message)}</p>
                    </div>
                </div>
            `);
            scrollToBottomStaff();
            saveChatHistoryStaff();
        }

        function formatTime(timeString) {
            if (!timeString) return '';
            const d = new Date(timeString);
            if (isNaN(d.getTime())) return '';
            const pad = (n) => String(n).padStart(2, '0');
            return `${pad(d.getHours())}:${pad(d.getMinutes())} ${pad(d.getDate())}/${pad(d.getMonth() + 1)}`;
        }

        function pollTicketMessages() {
            const ticketId = getTicketId();
            if (!ticketId) return;

            $.ajax({
                url: `/api/tickets/${ticketId}/messages`,
                type: 'GET',
                success: function(response) {
                    if (!response || !response.success || !Array.isArray(response.messages)) return;
                    let currentLastId = getLastMessageId();
                    const newMessages = response.messages.filter(msg => Number(msg.id) > currentLastId);
                    newMessages.forEach(function(msg) {
                        if (Number(msg.id) > currentLastId) currentLastId = Number(msg.id);
                        if (msg.sender_type === 'admin') {
                            appendAdminReply(msg.sender_name, msg.message, formatTime(msg.created_at));
                            // Cập nhật chấm thông báo sang đỏ khi có tin nhắn từ nhân viên
                            updateNotificationDotToRed();
                        }
                    });
                    if (currentLastId > getLastMessageId()) setLastMessageId(currentLastId);
                }
            });
        }

        function startPolling() {
            if (pollingTimer) return;
            pollingTimer = setInterval(pollTicketMessages, 5000);
            pollTicketMessages();
        }

        function stopPolling() {
            if (!pollingTimer) return;
            clearInterval(pollingTimer);
            pollingTimer = null;
        }

        function updateStaffUI() {
            const hasTicket = !!getTicketId();
            if (hasTicket) {
                $('#staff-connect-prompt').addClass('hidden');
                $('#staff-chat-content').removeClass('hidden').addClass('flex');
                $('#chat-input-staff').prop('disabled', false).attr('placeholder', 'Nhập tin nhắn...');
                $('#send-chat-staff').prop('disabled', false);
                $('#end-staff-chat-btn').removeClass('hidden');
            } else {
                $('#staff-connect-prompt').removeClass('hidden').addClass('flex');
                $('#staff-chat-content').removeClass('flex').addClass('hidden');
                $('#chat-input-staff').prop('disabled', true).attr('placeholder', 'Vui lòng kết nối nhân viên');
                $('#send-chat-staff').prop('disabled', true);
                $('#end-staff-chat-btn').addClass('hidden');
            }
        }

        // Restore chat history
        if (sessionStorage.getItem('beephone_chat_history_ai')) {
            $('#chat-box-ai').html(sessionStorage.getItem('beephone_chat_history_ai'));
            scrollToBottomAi();
        }
        if (sessionStorage.getItem('beephone_chat_history_staff')) {
            $('#staff-chat-content').html(sessionStorage.getItem('beephone_chat_history_staff'));
            scrollToBottomStaff();
        }

        if (getTicketId()) {
            startPolling();
        }
        updateStaffUI();

        // AI Send
        function sendAiMessage() {
            const message = $('#chat-input-ai').val().trim();
            if (message === '') return;
            
            $('#send-chat-ai').prop('disabled', true).css('opacity', '0.5');
            $('#chat-input-ai').prop('disabled', true).attr('placeholder', 'AI đang suy nghĩ...');

            $('#chat-box-ai').append(`
                <div class="flex items-start gap-2 justify-end">
                    <div class="bg-[#181611] text-white p-3 rounded-2xl rounded-tr-none shadow-sm max-w-[80%]">
                        ${escapeHtml(message)}
                    </div>
                </div>
            `);
            $('#chat-input-ai').val('');
            scrollToBottomAi();
            saveChatHistoryAi();

            // Save to context
            let chatContext = JSON.parse(sessionStorage.getItem('beephone_chat_context_ai') || '[]');
            chatContext.push({ role: 'user', text: message });
            sessionStorage.setItem('beephone_chat_context_ai', JSON.stringify(chatContext));

            const loadingId = 'loading-' + Date.now();
            $('#chat-box-ai').append(`
                <div id="${loadingId}" class="flex items-start gap-2">
                    <div class="w-8 h-8 rounded-full bg-[#f4c025] flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[#181611] text-sm">smart_toy</span>
                    </div>
                    <div class="bg-white dark:bg-slate-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-gray-500 italic max-w-[80%] animate-pulse">
                        Đang xử lý...
                    </div>
                </div>
            `);
            scrollToBottomAi();

            $.ajax({
                url: "{{ route('chatbot.chat') }}",
                type: "POST",
                data: { 
                    _token: "{{ csrf_token() }}", 
                    message: message,
                    history: JSON.stringify(chatContext)
                },
                success: function(response) {
                    $('#' + loadingId).remove();

                    // Save AI reply to context
                    if (response.reply) {
                        chatContext.push({ role: 'model', text: response.reply });
                        sessionStorage.setItem('beephone_chat_context_ai', JSON.stringify(chatContext));
                    }

                    let replyHtml = `
                        <div class="flex items-start gap-2">
                            <div class="w-8 h-8 rounded-full bg-[#f4c025] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[#181611] text-sm">smart_toy</span>
                            </div>
                            <div class="bg-white dark:bg-slate-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-slate-800 dark:text-white max-w-[80%]">
                                ${response.reply}
                            </div>
                        </div>
                    `;

                    if (response.suggestions && response.suggestions.length > 0) {
                        replyHtml += '<div class="flex flex-wrap gap-2 mt-2 ml-10">';
                        response.suggestions.forEach(function(suggestion) {
                            replyHtml += `<button class="suggestion-btn text-xs bg-gray-100 dark:bg-slate-600 text-slate-700 dark:text-slate-300 px-3 py-1 rounded-full hover:bg-[#f4c025] hover:text-[#181611] transition-colors">${suggestion}</button>`;
                        });
                        replyHtml += '</div>';
                    }

                    replyHtml += renderProductList('Sản phẩm gợi ý', response.products);
                    replyHtml += renderProductList('Sản phẩm bán chạy', response.best_sellers);

                    $('#chat-box-ai').append(replyHtml);
                    scrollToBottomAi();
                    saveChatHistoryAi();

                    // Cập nhật chấm thông báo sang đỏ nếu chat window bị đóng
                    const chatWindow = document.getElementById('chat-window');
                    const chatBubble = document.getElementById('chat-bubble');
                    if (chatWindow && chatBubble && chatWindow.classList.contains('hidden')) {
                        updateNotificationDotToRed();
                    }

                    // Enable send button
                    $('#send-chat').prop('disabled', false).css('opacity', '1');
                    $('#chat-input').prop('disabled', false).attr('placeholder', 'Nhập câu hỏi...').focus();
                },
                error: function(xhr) {
                    $('#' + loadingId).remove();
                    let errorMsg = 'Lỗi kết nối máy chủ!';
                    if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                    $('#chat-box-ai').append(`<div class="text-center text-xs text-red-500 mt-2 font-bold">${errorMsg}</div>`);
                    scrollToBottomAi();
                    saveChatHistoryAi();
                    $('#send-chat-ai').prop('disabled', false).css('opacity', '1');
                    $('#chat-input-ai').prop('disabled', false).attr('placeholder', 'Hỏi AI...').focus();
                }
            });
        }

        // Staff Send
        function sendStaffMessage() {
            const message = $('#chat-input-staff').val().trim();
            if (message === '') return;
            const ticketId = getTicketId();
            if (!ticketId) return;

            $('#send-chat-staff').prop('disabled', true).css('opacity', '0.5');
            $('#chat-input-staff').prop('disabled', true).attr('placeholder', 'Đang gửi...');

            $('#staff-chat-content').append(`
                <div class="flex items-start gap-2 justify-end">
                    <div class="bg-blue-500 text-white p-3 rounded-2xl rounded-tr-none shadow-sm max-w-[80%]">
                        ${escapeHtml(message)}
                    </div>
                </div>
            `);
            $('#chat-input-staff').val('');
            scrollToBottomStaff();
            saveChatHistoryStaff();

            $.ajax({
                url: "{{ route('api.tickets.add-message') }}",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                data: {
                    ticket_id: ticketId,
                    sender_type: 'customer',
                    sender_name: getCustomerName(),
                    message: message
                },
                success: function() {
                    pollTicketMessages();
                    $('#send-chat-staff').prop('disabled', false).css('opacity', '1');
                    $('#chat-input-staff').prop('disabled', false).attr('placeholder', 'Nhập tin nhắn...').focus();
                },
                error: function(xhr) {
                    let errorMsg = 'Lỗi gửi tin nhắn!';
                    if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                    $('#staff-chat-content').append(`<div class="text-center text-xs text-red-500 mt-2 font-bold">${errorMsg}</div>`);
                    scrollToBottomStaff();
                    saveChatHistoryStaff();
                    $('#send-chat-staff').prop('disabled', false).css('opacity', '1');
                    $('#chat-input-staff').prop('disabled', false).attr('placeholder', 'Nhập tin nhắn...').focus();
                }
            });
        }

        // AI triggers
        $('#send-chat-ai').click(sendAiMessage);
        $('#chat-input-ai').keypress(function(e) { if (e.which === 13) { e.preventDefault(); sendAiMessage(); } });
        
        // Staff triggers
        $('#send-chat-staff').click(sendStaffMessage);
        $('#chat-input-staff').keypress(function(e) { if (e.which === 13) { e.preventDefault(); sendStaffMessage(); } });

        // Handle suggestion button clicks
        $(document).on('click', '.suggestion-btn', function() {
            const suggestion = $(this).text();
            $('#chat-input-ai').val(suggestion);
            sendAiMessage();
        });

        // Contact staff button
        $('#contact-staff-btn-new').click(function() {
            const formData = {
                customer_name: @json(optional(auth()->user())->name ?? 'Khách hàng'),
                customer_email: @json(optional(auth()->user())->email ?? 'guest@beephone.vn'),
                title: 'Yêu cầu kết nối với nhân viên',
                initial_message: 'Khách hàng cần được kết nối trực tiếp với nhân viên hỗ trợ.',
            };

            const $btn = $(this);
            $btn.prop('disabled', true).text('Đang kết nối...');

            $.ajax({
                url: "{{ route('api.tickets.create') }}",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        setTicketId(response.ticket_id);
                        
                        $('#staff-chat-content').append(`
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm">check_circle</span>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-2xl rounded-tl-none shadow-sm text-green-800 dark:text-green-200 max-w-[80%]">
                                    <p class="font-semibold">Đã kết nối với nhân viên hỗ trợ!</p>
                                    <p class="text-xs mt-1">Mã ticket: <strong>${escapeHtml(response.ticket_code)}</strong></p>
                                </div>
                            </div>
                        `);
                        saveChatHistoryStaff();
                        updateStaffUI();

                        $.ajax({
                            url: `/api/tickets/${response.ticket_id}/messages`,
                            type: 'GET',
                            success: function(res) {
                                if (res && res.success && Array.isArray(res.messages) && res.messages.length > 0) {
                                    const maxId = Math.max(...res.messages.map(m => Number(m.id) || 0));
                                    if (maxId > 0) setLastMessageId(maxId);
                                }
                                startPolling();
                            },
                            error: function() { startPolling(); }
                        });
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).text('Kết Nối Ngay');
                    let errorMsg = 'Lỗi gửi yêu cầu!';
                    if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                    alert('Lỗi: ' + errorMsg);
                }
            });
        });

        $('#end-staff-chat-btn').click(function() {
            if(confirm('Bạn có chắc chắn muốn kết thúc phiên chat với nhân viên?')) {
                sessionStorage.removeItem(CHAT_TICKET_ID_KEY);
                sessionStorage.removeItem(CHAT_LAST_MESSAGE_ID_KEY);
                sessionStorage.removeItem('beephone_chat_history_staff');
                stopPolling();
                $('#staff-chat-content').html('');
                updateStaffUI();
            }
        });
    });
</script>
