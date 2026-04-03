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
                Dạ em chào anh/chị! Em là trợ lý AI của BeePhone. Anh/chị đang tìm mua điện thoại khoảng bao nhiêu tiền ạ?
            </div>
        </div>
    </div>

    <div class="p-3 bg-white dark:bg-slate-900 border-t border-gray-100 dark:border-slate-800 flex items-center gap-2">
        <input type="text" id="chat-input" class="flex-1 bg-gray-100 dark:bg-slate-800 border-none rounded-full px-4 py-2 text-sm focus:ring-0 dark:text-white outline-none" placeholder="Nhập câu hỏi...">
        <button id="send-chat" class="w-10 h-10 bg-[#f4c025] rounded-full flex items-center justify-center text-[#181611] hover:brightness-105 transition-all">
            <span class="material-symbols-outlined text-sm">send</span>
        </button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Khôi phục lịch sử chat
        if (sessionStorage.getItem('beephone_chat_history')) {
            $('#chat-box').html(sessionStorage.getItem('beephone_chat_history'));
            scrollToBottom();
        }
        
        // Khôi phục trạng thái cửa sổ
        if (sessionStorage.getItem('beephone_chat_state') === 'open') {
            $('#chat-window').removeClass('hidden');
            $('#chat-bubble').addClass('hidden');
            scrollToBottom();
        }

        function saveChatHistory() {
            sessionStorage.setItem('beephone_chat_history', $('#chat-box').html());
        }

        $('#chat-bubble').click(function() {
            $('#chat-window').removeClass('hidden');
            $('#chat-bubble').addClass('hidden');
            sessionStorage.setItem('beephone_chat_state', 'open');
            scrollToBottom();
        });

        $('#close-chat').click(function() {
            $('#chat-window').addClass('hidden');
            $('#chat-bubble').removeClass('hidden');
            sessionStorage.setItem('beephone_chat_state', 'closed');
        });

        function sendMessage() {
            let message = $('#chat-input').val().trim();
            if (message === '') return;

            // 1. KHOÁ MÕM PHÍM NGAY LẬP TỨC
            $('#send-chat').prop('disabled', true).css('opacity', '0.5');
            $('#chat-input').prop('disabled', true).attr('placeholder', 'AI đang suy nghĩ...');

            // In tin nhắn của User
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

            // Hiệu ứng AI đang gõ
            let loadingId = 'loading-' + Date.now();
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

            // Gọi AJAX
            $.ajax({
                url: "{{ route('chatbot.chat') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: message
                },
                success: function(response) {
                    $('#' + loadingId).remove(); 
                    
                    $('#chat-box').append(`
                        <div class="flex items-start gap-2">
                            <div class="w-8 h-8 rounded-full bg-[#f4c025] flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[#181611] text-sm">smart_toy</span>
                            </div>
                            <div class="bg-white dark:bg-slate-700 p-3 rounded-2xl rounded-tl-none shadow-sm text-slate-800 dark:text-white max-w-[80%]">
                                ${response.reply}
                            </div>
                        </div>
                    `);

                    // Thêm quick replies nếu cần
                    if (message.toLowerCase().includes('khác') || message.toLowerCase().includes('không') || message.toLowerCase().includes('chưa']) {
                        setTimeout(() => {
                            $('#chat-box').append(`
                                <div class="flex items-start gap-2">
                                    <div class="w-8 h-8 rounded-full bg-[#f4c025] flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[#181611] text-sm">smart_toy</span>
                                    </div>
                                    <div class="bg-white dark:bg-slate-700 p-3 rounded-2xl rounded-tl-none shadow-sm max-w-[80%]">
                                        <p class="text-sm text-slate-600 dark:text-gray-300 mb-2">Anh/chị cần giúp gì khác không? Em có thể kết nối nhân viên hỗ trợ cho anh/chị ạ.</p>
                                        <div class="flex gap-2">
                                            <button class="bg-primary text-white px-3 py-2 rounded text-xs font-semibold hover:bg-primary-dark transition quick-reply-btn" data-message="Tôi muốn gặp nhân viên">Kết nối nhân viên</button>
                                            <button class="bg-gray-300 text-gray-700 px-3 py-2 rounded text-xs font-semibold hover:bg-gray-400 transition quick-reply-btn" data-message="Không, cảm ơn">Không cần</button>
                                        </div>
                                    </div>
                                </div>
                            `);
                            scrollToBottom();
                        }, 500);
                    }

                    scrollToBottom();
                    saveChatHistory(); 

                    // 2. MỞ KHOÁ LẠI CHO KHÁCH CHAT TIẾP
                    $('#send-chat').prop('disabled', false).css('opacity', '1');
                    $('#chat-input').prop('disabled', false).attr('placeholder', 'Nhập câu hỏi...').focus();
                },
                error: function(xhr, status, error) {
                    $('#' + loadingId).remove();
                    
                    // Lấy lỗi thật sự từ Laravel trả về
                    let errorMsg = "Lỗi kết nối máy chủ!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        errorMsg = "Phiên làm việc hết hạn, vui lòng F5 tải lại trang!";
                    }

                    // In cả lỗi màu đỏ ra Console để Dev dễ soi
                    console.error("LỖI BACKEND TRẢ VỀ:", xhr.responseText);

                    $('#chat-box').append(`<div class="text-center text-xs text-red-500 mt-2 font-bold">${errorMsg}</div>`);
                    scrollToBottom();
                    saveChatHistory();

                    // MỞ KHOÁ LẠI KHI LỖI
                    $('#send-chat').prop('disabled', false).css('opacity', '1');
                    $('#chat-input').prop('disabled', false).attr('placeholder', 'Nhập câu hỏi...').focus();
                }
            });
        }

        $('#send-chat').click(sendMessage);
        $('#chat-input').keypress(function(e) {
            if(e.which == 13) sendMessage();
        });

        // Handle quick reply buttons
        $(document).on('click', '.quick-reply-btn', function() {
            let message = $(this).data('message');
            $('#chat-input').val(message);
            sendMessage();
        });

        // Handle "Kết nối nhân viên" action
        $(document).on('click', '.connect-staff-btn', function() {
            alert('Đang kết nối bạn với nhân viên hỗ trợ... Vui lòng chờ!');
            // TODO: Thêm logic kết nối với staff (có thể gửi request lưu vào DB, notify staff app, etc)
            $('#chat-box').append(`
                <div class="flex items-start gap-2">
                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">person</span>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900 p-3 rounded-2xl rounded-tl-none shadow-sm text-green-800 dark:text-white max-w-[80%]">
                        Nhân viên hỗ trợ sẽ sớm trả lời anh/chị ạ. Anh/chị vui lòng chờ...
                    </div>
                </div>
            `);
            scrollToBottom();
            saveChatHistory();
        });

        function scrollToBottom() {
            let chatBox = document.getElementById("chat-box");
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }
    });

</script>