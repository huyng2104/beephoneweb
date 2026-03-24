<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('client.contact.index');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:100',
            'message' => 'required|string',
        ]);

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'category' => $validated['category'] ?? 'general',
            'message' => $validated['message'],
        ]);

        return redirect()->route('contact')->with('success', 'Gửi yêu cầu thành công. Chúng tôi sẽ liên hệ sớm.');
    }
}
