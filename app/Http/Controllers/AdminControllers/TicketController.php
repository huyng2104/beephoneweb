<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Ticket::query();

    //     if ($request->keyword) {
    //         $query->where('customer_name', 'like', '%' . $request->keyword . '%')
    //             ->orWhere('ticket_code', 'like', '%' . $request->keyword . '%');
    //     }

    //     if ($request->status) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->priority) {
    //         $query->where('priority', $request->priority);
    //     }

    //     $tickets = $query->latest()->paginate(10);

    //     return view('admin.tickets.index', compact('tickets'));
    // }

    public function index(Request $request)
{
    $query = Ticket::query();

    if ($request->keyword) {
        $query->where('customer_name', 'like', '%' . $request->keyword . '%')
              ->orWhere('ticket_code', 'like', '%' . $request->keyword . '%');
    }

    $tickets = $query->latest()->paginate(10);

    // THỐNG KÊ
    $totalTickets = Ticket::count();
    $pendingTickets = Ticket::where('status','new')->count();
    $processingTickets = Ticket::where('status','processing')->count();
    $doneTickets = Ticket::where('status','done')->count();

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

        return view('admin.tickets.show', compact('ticket'));
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
