<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('client.contact.index');
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'category' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        $contact = ContactMessage::create($data);

        // Tuỳ chọn: gửi email thông báo cho support (nếu cấu hình mail đã bật)
        // try {
        //     Mail::to(config('mail.support', 'support@beephone.vn'))->send(new \App\Mail\NewContactMessage($contact));
        // } catch (\Exception $e) {
        //     // không fail user flow
        // }

        return back()->with('success', 'Cảm ơn bạn! Yêu cầu đã gửi thành công. Chúng tôi sẽ phản hồi sớm nhất.');
    }
}
