<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatbotController;

Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('api.chatbot.chat');
Route::get('/chatbot/categories', [ChatbotController::class, 'getCategories'])->name('api.chatbot.categories');
Route::get('/chatbot/questions/{category}', [ChatbotController::class, 'getQuestions'])->name('api.chatbot.questions');

// ==========================================
// WEBHOOK GHN (ĐÃ VÔ HIỆU HÓA)
// Hệ thống đã chuyển sang dùng polling API (ghn:sync) thay vì webhook.
// Không xóa, để dự phòng khi cần bật lại.
// ==========================================
// use App\Http\Controllers\Api\GhnWebhookController;
// Route::post('/webhook/ghn', [GhnWebhookController::class, 'handle'])->name('api.webhook.ghn');
