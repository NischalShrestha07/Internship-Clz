<?php

namespace App\Models;

use App\Models\User;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveConfigure extends Model
{
    use HasFactory;
    protected $fillable = ['leave_type_id', 'employee_id', 'days'];
    protected $table = "employee_leaves";
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
    public function employee()
    {
         return $this->belongsTo(User::class, 'employee_id');
    }
}