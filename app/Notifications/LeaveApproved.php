<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveApproved extends Notification
{
    use Queueable;

    protected $leave;

    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $start = \Carbon\Carbon::parse($this->leave->start_date_time)->format('M d, Y');
        $end = \Carbon\Carbon::parse($this->leave->end_date_time)->format('M d, Y');

        return [
            'leave_id' => $this->leave->id,
            'type' => 'approved',
            'message' => "Your leave request from {$start} to {$end} has been approved",
            'leave_type' => $this->leave->leaveType->leave_type ?? 'Leave',
        ];
    }
}
