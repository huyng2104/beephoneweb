<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();

        // tìm kiếm
        if ($request->keyword) {
            $query->where('customer_name', 'like', '%' . $request->keyword . '%')
                ->orWhere('ticket_code', 'like', '%' . $request->keyword . '%');
        }

        $tickets = $query->latest()->paginate(10);

        // thống kê
        $totalTickets = Ticket::count();

        $pendingTickets = Ticket::where('status', 'new')->count();

        $processingTickets = Ticket::where('status', 'processing')->count();

        $doneTickets = Ticket::where('status', 'done')->count();

        return view('admin.tickets.index', compact(
            'tickets',
            'totalTickets',
            'pendingTickets',
            'processingTickets',
            'doneTickets'
        ));
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        $messages = TicketMessage::where('ticket_id', $id)->orderBy('created_at', 'asc')->get();

        return view('admin.tickets.show', compact('ticket', 'messages'));
    }

    public function addMessage(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        TicketMessage::create([
            'ticket_id' => $id,
            'sender_type' => 'admin',
            'sender_name' => auth()->user()->name,
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Phản hồi đã được gửi!');
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Cập nhật trạng thái thành công');
    }
}
