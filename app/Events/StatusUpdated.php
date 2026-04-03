<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $targetUserId;
    public $title;
    public $message;
    public $url;

    public function __construct($targetUserId, $title, $message, $url)
    {
        $this->targetUserId = $targetUserId;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    // Tên Kênh phát sóng (Khớp với window.Echo.channel('order-tracker') ở file JS)
    public function broadcastOn()
    {
        return new Channel('order-tracker');
    }

    // Tên sự kiện (Khớp với .listen('.status-updated') ở file JS)
    public function broadcastAs()
    {
        return 'status-updated';
    }
}