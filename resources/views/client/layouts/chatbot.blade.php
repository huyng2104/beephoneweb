<div id="chat-bubble" class="fixed bottom-6 right-6 bg-[#f4c025] p-4 rounded-full cursor-pointer shadow-xl hover:scale-110 transition-transform z-50 flex items-center justify-center group">
    <span class="material-symbols-outlined text-[#181611] text-3xl">smart_toy</span>
    <span class="absolute -top-1 -right-1 flex h-4 w-4">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
        <span class="relative inline-flex rounded-full h-4 w-4 bg-white border-2 border-[#f4c025]"></span>
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
        <button id="contact-staff-btn" class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white hover:brightness-105 transition-all" title="Liên hệ nhân viên">
            <span class="material-symbols-outlined text-sm">person</span>
        </button>
    </div>
</div>

<!-- Modal: Liên hệ nhân viên -->
<div id="contact-staff-modal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="bg-blue-500 text-white p-4 flex justify-between items-center">
            <h3 class="font-bold">Liên hệ nhân viên hỗ trợ</h3>
            <button id="close-staff-modal" class="text-white hover:opacity-80">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form id="staff-contact-form" class="p-4 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">Tên của bạn <span class="text-red-500">*</span></label>
                <input type="text" id="staff_name" name="customer_name" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" id="staff_email" name="customer_email" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">Tiêu đề vấn đề <span class="text-red-500">*</span></label>
                <input type="text" id="staff_title" name="title" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ví dụ: Cần hỗ trợ bảo hành" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">Mô tả vấn đề <span class="text-red-500">*</span></label>
                <textarea id="staff_message" name="initial_message" class="w-full px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Mô tả chi tiết vấn đề của bạn..." required></textarea>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold hover:brightness-110 transition-all">
                    Gửi yêu cầu
                </button>
                <button type="button" id="cancel-staff-modal" class="flex-1 px-4 py-2 bg-slate-300 dark:bg-slate-600 text-slate-800 dark:text-white rounded-lg font-semibold hover:opacity-80 transition-all">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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

        // Restore chat history
        if (sessionStorage.getItem('beephone_chat_history')) {
            $('#chat-box').html(sessionStorage.getItem('beephone_chat_history'));
            scrollToBottom();
        }

        if (getTicketId()) {
            startPolling();
        }

        // XỬ LÝ GỬI TIN NHẮN (Đã tích hợp Lớp Giáp chống Spam)
        function sendMessage() {
            const message = $('#chat-input').val().trim();
            if (message === '') return;

            // Disable send button
            $('#send-chat').prop('disabled', true).css('opacity', '0.5');
            $('#chat-input').prop('disabled', true).attr('placeholder', 'AI đang suy nghĩ...');

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

                    $('#chat-box').append(replyHtml);
                    scrollToBottom();
                    saveChatHistory();

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

        // ===== MODAL: Liên hệ nhân viên =====
        $('#contact-staff-btn').click(function() {
            $('#contact-staff-modal').removeClass('hidden').addClass('flex');
        });

        $('#close-staff-modal, #cancel-staff-modal').click(function() {
            $('#contact-staff-modal').addClass('hidden').removeClass('flex');
        });

        // Close modal when clicking outside
        $('#contact-staff-modal').click(function(e) {
            if (e.target === this) {
                $('#contact-staff-modal').addClass('hidden').removeClass('flex');
            }
        });

        // Submit form: Tạo ticket
        $('#staff-contact-form').submit(function(e) {
            e.preventDefault();

            const formData = {
                customer_name: $('#staff_name').val(),
                customer_email: $('#staff_email').val(),
                title: $('#staff_title').val(),
                initial_message: $('#staff_message').val(),
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
                        // Lưu ticket id để đồng bộ phản hồi từ admin
                        setTicketId(response.ticket_id);

                        // Gắn mốc message đầu tiên để chỉ lấy phản hồi mới sau này
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

                        // Hiển thị thông báo thành công
                        $('#chat-box').append(`
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm">check_circle</span>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-2xl rounded-tl-none shadow-sm text-green-800 dark:text-green-200 max-w-[80%]">
                                    <p class="font-semibold">Yêu cầu đã được gửi!</p>
                                    <p class="text-xs mt-1">Mã ticket: <strong>${response.ticket_code}</strong></p>
                                    <p class="text-xs mt-1">Nhân viên hỗ trợ sẽ liên hệ anh/chị sớm nhất. Cảm ơn!</p>
                                </div>
                            </div>
                        `);

                        // Reset form và đóng modal
                        $('#staff-contact-form')[0].reset();
                        $('#contact-staff-modal').addClass('hidden').removeClass('flex');
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