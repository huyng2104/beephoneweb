<div id="chat-bubble" class="fixed bottom-6 right-6 bg-[#f4c025] p-4 rounded-full cursor-pointer shadow-xl hover:scale-110 transition-transform z-50 flex items-center justify-center group">
    <span class="material-symbols-outlined text-[#181611] text-3xl">smart_toy</span>
    <span id="notification-dot" class="absolute -top-1 -right-1 flex h-4 w-4">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
        <span class="notification-dot-inner relative inline-flex rounded-full h-4 w-4 bg-white border-2 border-[#f4c025]"></span>
    </span>
</div>

<div id="chat-window" class="fixed bottom-24 right-6 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800 flex-col hidden z-50 overflow-hidden flex">
    <div class="bg-[#181611] dark:bg-black text-white p-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[#f4c025]">smart_toy</span>
            <div>
                <h3 class="font-bold text-sm">AI Tư Vấn - BeePhone</h3>
                <p class="text-xs text-green-400 flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-400"></span> Trực tuyến</p>
            </div>
        </div>
        <button id="close-chat" class="text-gray-400 hover:text-white"><span class="material-symbols-outlined">close</span></button>
    </div>
    
    <div id="chat-box" class="h-80 p-4 overflow-y-auto bg-gray-50 dark:bg-slate-800 flex flex-col gap-3 text-sm">
        <div class="flex items-start gap-2">
            <div class="w-8 h-8 rounded-full bg-[#f4c025] flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[#181611] text-sm">smart_toy</span>
            </div>
            <div class="bg-white dark:bg-slate-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-slate-800 dark:text-white max-w-[80%]">
                Dạ em chào anh/chị! Em là trợ lý AI của BeePhone. Anh/chị đang cần hỗ trợ về sản phẩm hay dịch vụ nào ạ? Anh/chị có thể hỏi về bảo hành, kỹ thuật, đặt hàng hoặc bất cứ điều gì liên quan đến BeePhone nhé!
            </div>
        </div>
    </div>

    <div class="p-3 bg-white dark:bg-slate-900 border-t border-gray-100 dark:border-slate-800 flex items-center gap-2">
        <input type="text" id="chat-input" class="flex-1 bg-gray-100 dark:bg-slate-800 border-none rounded-full px-4 py-2 text-sm focus:ring-0 dark:text-white outline-none" placeholder="Nhập câu hỏi...">
        <button id="send-chat" class="w-10 h-10 bg-[#f4c025] rounded-full flex items-center justify-center text-[#181611] hover:brightness-105 transition-all">
            <span class="material-symbols-outlined text-sm">send</span>
        </button>
        <button id="contact-staff-btn" class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white hover:brightness-105 transition-all" title="Kết nối với nhân viên">
            <span class="material-symbols-outlined text-sm">person</span>
        </button>
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
        let pollingTimer = null;

        function saveChatHistory() {
            sessionStorage.setItem('beephone_chat_history', $('#chat-box').html());
        }

        function scrollToBottom() {
            const chatBox = document.getElementById('chat-box');
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }

        function escapeHtml(text) {
            return $('<div>').text(text ?? '').html();
        }

        function getTicketId() {
            return sessionStorage.getItem(CHAT_TICKET_ID_KEY);
        }

        function setTicketId(ticketId) {
            if (ticketId) {
                sessionStorage.setItem(CHAT_TICKET_ID_KEY, String(ticketId));
            }
        }

        function getLastMessageId() {
            const id = sessionStorage.getItem(CHAT_LAST_MESSAGE_ID_KEY);
            return id ? Number(id) : 0;
        }

        function setLastMessageId(id) {
            if (id) {
                sessionStorage.setItem(CHAT_LAST_MESSAGE_ID_KEY, String(id));
            }
        }

        function getCustomerName() {
            return @json(optional(auth()->user())->name ?? 'Khách hàng');
        }

        function formatPriceVnd(value) {
            const n = Number(value || 0);
            return `${n.toLocaleString('vi-VN')}đ`;
        }

        function renderProductList(title, items) {
            if (!Array.isArray(items) || items.length === 0) return '';

            const cards = items.slice(0, 3).map(function(item) {
                const thumbStyle = item.thumbnail
                    ? `style="background-image:url('${escapeHtml(item.thumbnail)}')"`
                    : '';
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
            $('#chat-box').append(`
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
            scrollToBottom();
            saveChatHistory();
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
                        if (Number(msg.id) > currentLastId) {
                            currentLastId = Number(msg.id);
                        }

                        if (msg.sender_type === 'admin') {
                            appendAdminReply(msg.sender_name, msg.message, formatTime(msg.created_at));
                            // Cập nhật chấm thông báo sang đỏ khi có tin nhắn từ nhân viên
                            updateNotificationDotToRed();
                        }
                    });

                    if (currentLastId > getLastMessageId()) {
                        setLastMessageId(currentLastId);
                    }
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

        function updateContactStaffButton() {
            const hasTicket = !!getTicketId();
            const $btn = $('#contact-staff-btn');
            const $icon = $btn.find('.material-symbols-outlined');

            if (hasTicket) {
                $btn.attr('title', 'Quay lại chat với AI');
                $icon.text('smart_toy');
            } else {
                $btn.attr('title', 'Kết nối với nhân viên');
                $icon.text('support_agent');
            }
        }

        // Restore chat history
        if (sessionStorage.getItem('beephone_chat_history')) {
            $('#chat-box').html(sessionStorage.getItem('beephone_chat_history'));
            scrollToBottom();
        }

        if (getTicketId()) {
            startPolling();
            $('#chat-input').attr('placeholder', 'Nhập nội dung cho nhân viên...');
        }
        updateContactStaffButton();

        function sendMessage() {
            const message = $('#chat-input').val().trim();
            if (message === '') return;
            const ticketId = getTicketId();

            // Disable send button
            $('#send-chat').prop('disabled', true).css('opacity', '0.5');
            $('#chat-input').prop('disabled', true).attr('placeholder', ticketId ? 'Đang gửi tới nhân viên...' : 'AI đang suy nghĩ...');

            // Show user message
            $('#chat-box').append(`
                <div class="flex items-start gap-2 justify-end">
                    <div class="bg-[#181611] text-white p-3 rounded-2xl rounded-tr-none shadow-sm max-w-[80%]">
                        ${message}
                    </div>
                </div>
            `);
            $('#chat-input').val('');
            scrollToBottom();
            saveChatHistory();

            // Khi đã kết nối nhân viên thì không gọi AI nữa
            if (ticketId) {
                $.ajax({
                    url: "{{ route('api.tickets.add-message') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        ticket_id: ticketId,
                        sender_type: 'customer',
                        sender_name: getCustomerName(),
                        message: message
                    },
                    success: function() {
                        pollTicketMessages();
                        $('#send-chat').prop('disabled', false).css('opacity', '1');
                        $('#chat-input').prop('disabled', false).attr('placeholder', 'Nhập nội dung cho nhân viên...').focus();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Lỗi gửi tin nhắn tới nhân viên!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        $('#chat-box').append(`
                            <div class="text-center text-xs text-red-500 mt-2 font-bold">
                                ${errorMsg}
                            </div>
                        `);
                        scrollToBottom();
                        saveChatHistory();

                        $('#send-chat').prop('disabled', false).css('opacity', '1');
                        $('#chat-input').prop('disabled', false).attr('placeholder', 'Nhập nội dung cho nhân viên...').focus();
                    }
                });
                return;
            }

            // Show loading
            const loadingId = 'loading-' + Date.now();
            $('#chat-box').append(`
                <div id="${loadingId}" class="flex items-start gap-2">
                    <div class="w-8 h-8 rounded-full bg-[#f4c025] flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[#181611] text-sm">smart_toy</span>
                    </div>
                    <div class="bg-white dark:bg-slate-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-gray-500 italic max-w-[80%] animate-pulse">
                        Đang xử lý...
                    </div>
                </div>
            `);
            scrollToBottom();

            // Send request
            $.ajax({
                url: "{{ route('chatbot.chat') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: message
                },
                success: function(response) {
                    $('#' + loadingId).remove();

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

                    // Add suggestions if available
                    if (response.suggestions && response.suggestions.length > 0) {
                        replyHtml += '<div class="flex flex-wrap gap-2 mt-2 ml-10">';
                        response.suggestions.forEach(function(suggestion) {
                            replyHtml += `<button class="suggestion-btn text-xs bg-gray-100 dark:bg-slate-600 text-slate-700 dark:text-slate-300 px-3 py-1 rounded-full hover:bg-[#f4c025] hover:text-[#181611] transition-colors">${suggestion}</button>`;
                        });
                        replyHtml += '</div>';
                    }

                    replyHtml += renderProductList('San pham phu hop', response.products);
                    replyHtml += renderProductList('San pham ban chay', response.best_sellers);

                    $('#chat-box').append(replyHtml);
                    scrollToBottom();
                    saveChatHistory();

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
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        errorMsg = 'Phiên làm việc hết hạn, vui lòng F5 tải lại trang!';
                    }

                    $('#chat-box').append(`
                        <div class="text-center text-xs text-red-500 mt-2 font-bold">
                            ${errorMsg}
                        </div>
                    `);
                    scrollToBottom();
                    saveChatHistory();

                    // Enable send button
                    $('#send-chat').prop('disabled', false).css('opacity', '1');
                    $('#chat-input').prop('disabled', false).attr('placeholder', 'Nhập câu hỏi...').focus();
                }
            });
        }

        // Send on button click
        $('#send-chat').click(function() {
            sendMessage();
        });

        // Send on Enter key
        $('#chat-input').keypress(function(e) {
            if (e.which === 13) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Handle suggestion button clicks
        $(document).on('click', '.suggestion-btn', function() {
            const suggestion = $(this).text();
            $('#chat-input').val(suggestion);
            sendMessage();
        });

        // Quick reply buttons
        $(document).on('click', '.quick-reply-btn', function() {
            const msg = $(this).data('message');
            $('#chat-input').val(msg);
            sendMessage();
        });

        // ===== KẾT NỐI NHÂN VIÊN TRỰC TIẾP =====
        $('#contact-staff-btn').click(function() {
            const existingTicketId = getTicketId();
            if (existingTicketId) {
                sessionStorage.removeItem(CHAT_TICKET_ID_KEY);
                sessionStorage.removeItem(CHAT_LAST_MESSAGE_ID_KEY);
                stopPolling();
                updateContactStaffButton();

                $('#chat-box').append(`
                    <div class="flex items-start gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#f4c025] flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[#181611] text-sm">smart_toy</span>
                        </div>
                        <div class="bg-white dark:bg-slate-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-slate-800 dark:text-white max-w-[80%]">
                            Đã chuyển về chế độ chat với AI. Anh/chị có thể tiếp tục hỏi trợ lý BeePhone.
                        </div>
                    </div>
                `);
                $('#chat-input').attr('placeholder', 'Nhập câu hỏi...');
                scrollToBottom();
                saveChatHistory();
                return;
            }

            const formData = {
                customer_name: @json(optional(auth()->user())->name ?? 'Khách hàng'),
                customer_email: @json(optional(auth()->user())->email ?? 'guest@beephone.vn'),
                title: 'Yêu cầu kết nối với nhân viên',
                initial_message: 'Khách hàng cần được kết nối trực tiếp với nhân viên hỗ trợ.',
            };

            $.ajax({
                url: "{{ route('api.tickets.create') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        setTicketId(response.ticket_id);
                        updateContactStaffButton();

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
                            error: function() {
                                startPolling();
                            }
                        });

                        $('#chat-box').append(`
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm">check_circle</span>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-2xl rounded-tl-none shadow-sm text-green-800 dark:text-green-200 max-w-[80%]">
                                    <p class="font-semibold">Đã kết nối với nhân viên hỗ trợ!</p>
                                    <p class="text-xs mt-1">Mã ticket: <strong>${response.ticket_code}</strong></p>
                                    <p class="text-xs mt-1">Nhân viên sẽ phản hồi ngay trong khung chat này.</p>
                                </div>
                            </div>
                        `);

                        scrollToBottom();
                        saveChatHistory();
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Lỗi gửi yêu cầu!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    alert('Lỗi: ' + errorMsg);
                }
            });
        });
    });

</script>