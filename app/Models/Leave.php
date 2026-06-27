<?php

namespace App\Models;

use App\Models\User;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = ['leave_type_id', 'leave_reason', 'status', 'remarks', 'employee_id', 'start_date_time', 'end_date_time'];
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
