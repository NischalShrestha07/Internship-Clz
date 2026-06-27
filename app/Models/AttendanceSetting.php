<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'daily_working_hours',
        'default_break_minutes',
        'overtime_threshold',
        'late_grace_minutes',
        'records_per_page',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public static function getSettings()
    {
        return static::firstOrCreate([]);
    }
}
