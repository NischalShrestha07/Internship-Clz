<?php

namespace App\Models;

use App\Models\LeaveConfiguration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveGroup extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected $table = 'leave_groups';
    public function leaveConfigurations()
    {
        return $this->hasMany(LeaveConfiguration::class, 'leave_group_id');
    }
}
