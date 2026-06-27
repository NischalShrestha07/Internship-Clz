<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PunchLog extends Model
{

    protected $fillable = ['attendance_id', 'punch_in', 'punch_out', 'notes'];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
