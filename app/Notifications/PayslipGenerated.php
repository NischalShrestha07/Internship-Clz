<?php

namespace App\Notifications;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PayslipGenerated extends Notification
{
    use Queueable;

    protected $payroll;

    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'payroll_id' => $this->payroll->id,
            'period' => $this->payroll->period,
            'message' => "Your payslip for {$this->payroll->period} has been generated",
        ];
    }
}
