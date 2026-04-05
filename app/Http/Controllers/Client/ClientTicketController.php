<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Str;

class ClientTicketController extends Controller
{
    // Form gửi hỗ trợ
    public function create()
    {
        return view('client.tickets.index');
    }

    // Lưu ticket
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'customer_phone' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        Ticket::create([
            'ticket_code' => 'TCK-' . strtoupper(Str::random(6)),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'new'
        ]);

        return redirect()->back()->with('success', 'Yêu cầu hỗ trợ đã được gửi!');
    }
}
