<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketMessage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::query();

        // tìm kiếm
        if ($request->keyword) {
            $keyword = trim((string) $request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('customer_email', 'like', '%' . $keyword . '%')
                    ->orWhere('ticket_code', 'like', '%' . $keyword . '%')
                    ->orWhere('subject', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('status') && in_array($request->status, ['open', 'in_progress', 'resolved', 'closed'], true)) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(10);

        // thống kê
        $totalTickets = SupportTicket::count();

        $pendingTickets = SupportTicket::where('status', 'open')->count();

        $processingTickets = SupportTicket::where('status', 'in_progress')->count();

        $doneTickets = SupportTicket::whereIn('status', ['resolved', 'closed'])->count();

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
        $ticket = SupportTicket::findOrFail($id);
        $messages = TicketMessage::where('ticket_id', $id)->orderBy('created_at', 'asc')->get();

        return view('admin.tickets.show', compact('ticket', 'messages'));
    }

    public function addMessage(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        TicketMessage::create([
            'ticket_id' => $id,
            'sender_type' => 'admin',
            'sender_name' => auth()->user()->name,
            'message' => $validated['message'],
        ]);

        $updates = [
            'response_count' => $ticket->response_count + 1,
        ];

        if ($ticket->status === 'open') {
            $updates['status'] = 'in_progress';
            if (!$ticket->first_response_at) {
                $updates['first_response_at'] = now();
            }
        }

        $ticket->update($updates);

        return back()->with('success', 'Phản hồi đã được gửi!');
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $updates = ['status' => $validated['status']];
        if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
            $updates['resolved_at'] = now();
        }
        if ($validated['status'] === 'closed' && !$ticket->closed_at) {
            $updates['closed_at'] = now();
        }

        $ticket->update($updates);

        return back()->with('success', 'Cập nhật trạng thái thành công');
    }
}
