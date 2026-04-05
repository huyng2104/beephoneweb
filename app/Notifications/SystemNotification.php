<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $url;

    // Nhận dữ liệu truyền vào
    public function __construct($title, $message, $url = '#')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    // Chỉ lưu vào Database
    public function via($notifiable)
    {
        return ['database'];
    }

    // Định dạng dữ liệu lưu vào DB
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}