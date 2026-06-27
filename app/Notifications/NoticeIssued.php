<?php

namespace App\Notifications;

use App\Models\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NoticeIssued extends Notification
{
    use Queueable;

    protected $notice;

    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'notice_id' => $this->notice->id,
            'notice_type' => $this->notice->notice_type,
            'message' => 'New notice: ' . \Illuminate\Support\Str::limit($this->notice->message, 100),
        ];
    }
}
