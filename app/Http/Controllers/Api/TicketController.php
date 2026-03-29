<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Tạo ticket mới từ chatbot
     */
    public function createFromChat(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'title' => 'required|string|max:255',
            'initial_message' => 'required|string',
        ]);

        try {
            // Tạo ticket mới
            $ticket = Ticket::create([
                'ticket_code' => 'TK-' . strtoupper(Str::random(8)),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'title' => $validated['title'],
                'description' => $validated['initial_message'],
                'priority' => 'medium',
                'status' => 'new',
            ]);

            // Lưu message đầu tiên
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'customer',
                'sender_name' => $validated['customer_name'],
                'message' => $validated['initial_message'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket đã được tạo thành công!',
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo ticket: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Thêm message vào ticket
     */
    public function addMessage(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'sender_type' => 'required|in:customer,admin',
            'sender_name' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            TicketMessage::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Message đã được lưu!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lưu message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy messages của ticket
     */
    public function getMessages($ticketId)
    {
        try {
            $messages = TicketMessage::where('ticket_id', $ticketId)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy messages: ' . $e->getMessage(),
            ], 500);
        }
    }
}
