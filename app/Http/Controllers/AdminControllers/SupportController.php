<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\SupportTicket;
use App\Models\TicketResponse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    /**
     * Display a listing of support tickets.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::query();

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned staff
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search by ticket code or customer name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $tickets = $query->paginate(15);
        $staff = User::where('role_id', 3)->get(); // Assuming role_id 3 is staff

        // Calculate statistics
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'high_priority' => SupportTicket::highPriority()->count(),
            'avg_satisfaction' => SupportTicket::whereNotNull('satisfaction_rating')->avg('satisfaction_rating') ?? 0,
            'avg_response_time' => $this->calculateAverageResponseTime(),
        ];

        return view('admin.support.index', compact('tickets', 'staff', 'stats'));
    }

    /**
     * Show the form for creating a new support ticket.
     */
    public function create()
    {
        return view('admin.support.create');
    }

    /**
     * Store a newly created support ticket in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:payment,warranty,shipping,return,general,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket = SupportTicket::create([
            'ticket_code' => $this->generateTicketCode(),
            ...$validated,
        ]);

        return redirect()->route('admin.support.show', $ticket)->with('success', 'Vé hỗ trợ đã được tạo thành công');
    }

    /**
     * Display the specified support ticket.
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'assignedTo', 'responses.user']);
        $staff = User::where('role_id', 3)->get();
        return view('admin.support.show', compact('ticket', 'staff'));
    }

    /**
     * Show the form for editing the specified support ticket.
     */
    public function edit(SupportTicket $ticket)
    {
        $staff = User::where('role_id', 3)->get();
        return view('admin.support.edit', compact('ticket', 'staff'));
    }

    /**
     * Update the specified support ticket in storage.
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'satisfaction_rating' => 'nullable|integer|min:1|max:5',
            'satisfaction_comment' => 'nullable|string',
        ]);

        $ticket->update($validated);

        return redirect()->route('admin.support.show', $ticket)->with('success', 'Vé hỗ trợ đã được cập nhật');
    }

    /**
     * Remove the specified support ticket from storage.
     */
    public function destroy(SupportTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.support.index')->with('success', 'Vé hỗ trợ đã được xóa');
    }

    /**
     * Add a response to a ticket.
     */
    public function addResponse(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'attachment_path' => 'nullable|file|max:5120', // 5MB max
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment_path')) {
            $attachmentPath = $request->file('attachment_path')->store('support-tickets', 'public');
        }

        TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_staff_response' => true,
            'attachment_path' => $attachmentPath,
        ]);

        // Update ticket response count and status
        $ticket->increment('response_count');
        if ($ticket->status === 'open') {
            $ticket->setStatusToInProgress();
        }

        return back()->with('success', 'Phản hồi đã được gửi');
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $status = $validated['status'];

        if ($status === 'in_progress' && $ticket->status === 'open') {
            $ticket->setStatusToInProgress();
        } elseif ($status === 'resolved') {
            $ticket->setStatusToResolved();
        } elseif ($status === 'closed') {
            $ticket->setStatusToClosed();
        } else {
            $ticket->update(['status' => $status]);
        }

        return back()->with('success', 'Trạng thái vé đã được cập nhật');
    }

    /**
     * Assign ticket to staff.
     */
    public function assignStaff(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket->update($validated);

        return back()->with('success', 'Vé đã được gán cho nhân viên');
    }

    /**
     * Generate unique ticket code.
     */
    private function generateTicketCode()
    {
        $prefix = 'BP';
        $lastTicket = SupportTicket::orderBy('id', 'desc')->first();
        $number = ($lastTicket ? (int)substr($lastTicket->ticket_code, 3) : 1000) + 1;
        return "{$prefix}-{$number}";
    }

    /**
     * Calculate average response time in minutes.
     */
    private function calculateAverageResponseTime()
    {
        $tickets = SupportTicket::whereNotNull('first_response_at')->get();
        if ($tickets->isEmpty()) {
            return 0;
        }

        $totalTime = $tickets->sum(function ($ticket) {
            return $ticket->first_response_at->diffInMinutes($ticket->created_at);
        });

        return round($totalTime / $tickets->count(), 2);
    }
}
