<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatbotController;

Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('api.chatbot.chat');
Route::get('/chatbot/categories', [ChatbotController::class, 'getCategories'])->name('api.chatbot.categories');
Route::get('/chatbot/questions/{category}', [ChatbotController::class, 'getQuestions'])->name('api.chatbot.questions');

