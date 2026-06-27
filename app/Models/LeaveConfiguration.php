<?php

namespace App\Models;

use App\Models\LeaveType;
use App\Models\LeaveGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveConfiguration extends Model
{
    use HasFactory;
    protected $fillable = ['leave_type_id', 'leave_group_id', 'days'];
    protected $table = "leave_configurations";
    public function leaveGroup()
    {
        return $this->belongsTo(LeaveGroup::class, 'leave_group_id');
    }
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
