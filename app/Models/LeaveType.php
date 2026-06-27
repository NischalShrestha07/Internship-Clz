<?php

namespace App\Models;

use App\Models\Leave;
use App\Models\LeaveConfiguration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;
    protected $fillable = ['leave_type', 'default_days'];
    protected $table = "leave_types";
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'leave_type_id');
    }
    public function leaveConfigurations()
    {
        return $this->hasMany(LeaveConfiguration::class, 'leave_type_id');
    }
}
