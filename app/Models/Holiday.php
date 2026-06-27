<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'is_recurring',
        'recurrence_pattern',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getHolidayDatesAttribute()
    {
        $dates = [];
        $current = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return $dates;
    }

    // Check if a specific date is covered by this holiday
    public function coversDate($date)
    {
        $date = Carbon::parse($date);
        return $date->between($this->start_date, $this->end_date);
    }
}
