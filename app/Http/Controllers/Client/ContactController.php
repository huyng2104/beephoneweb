<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        if (view()->exists('client.contact.index')) {
            return view('client.contact.index');
        }

        return response('Contact form (not implemented yet)', 200);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // TODO: Save contact message or send email

        return redirect()->route('contact')->with('success', 'Gửi yêu cầu thành công. Chúng tôi sẽ liên hệ sớm.');
    }
}
