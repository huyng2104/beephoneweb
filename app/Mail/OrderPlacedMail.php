<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        // Load sẵn danh sách sản phẩm để mang ra View in
        $this->order = $order->load('items');
    }

    public function build()
    {
        return $this->subject('Xác nhận đơn hàng #' . $this->order->order_code . ' từ Bee Phone')
                    ->view('emails.order_placed'); // File giao diện email
    }
}