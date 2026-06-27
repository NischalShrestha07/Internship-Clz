<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'time',
        'type',
        'priority',
        'description',
        'user_id',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_interval',
        'recurrence_weekdays',
        'end_date',
        'is_holiday'
    ];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_holiday' => 'boolean',
        'recurrence_weekdays' => 'array',
    ];

    // Recurrence pattern constants
    const RECURRENCE_NONE = 'none';
    const RECURRENCE_DAILY = 'daily';
    const RECURRENCE_WEEKLY = 'weekly';
    const RECURRENCE_MONTHLY = 'monthly';
    const RECURRENCE_YEARLY = 'yearly';
    const RECURRENCE_WEEKDAYS = 'weekdays';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRecurrenceDescriptionAttribute()
    {
        if (!$this->is_recurring) {
            return null;
        }

        $patterns = [
            self::RECURRENCE_DAILY => 'Daily',
            self::RECURRENCE_WEEKLY => 'Weekly',
            self::RECURRENCE_MONTHLY => 'Monthly',
            self::RECURRENCE_YEARLY => 'Yearly',
            self::RECURRENCE_WEEKDAYS => 'Weekdays',
        ];

        $description = $patterns[$this->recurrence_pattern] ?? $this->recurrence_pattern;

        // Handle interval display - FIX: Add support for 0 interval
        if ($this->recurrence_interval > 1) {
            $interval = $this->recurrence_interval;
            $pattern = str_replace('ly', '', $this->recurrence_pattern);
            $description = "Every {$interval} " . ($interval > 1 ? "{$pattern}s" : $pattern);
        } elseif ($this->recurrence_interval === 0 && $this->recurrence_pattern === 'weekly') {
            $description = "Every week"; // Special case for weekly with 0 interval
        } elseif ($this->recurrence_interval === 1) {
            $description = "Every " . str_replace('ly', '', $this->recurrence_pattern);
        }

        // Add weekday information for weekly recurrence
        if ($this->recurrence_pattern === 'weekly' && !empty($this->recurrence_weekdays)) {
            $weekdays = array_map('ucfirst', $this->recurrence_weekdays);
            $description .= ' on ' . implode(', ', $weekdays);
        }

        if ($this->end_date) {
            $description .= ' until ' . $this->end_date->format('M j, Y');
        }

        return $description;
    }

    public function getNextOccurrence()
    {
        if (!$this->is_recurring) {
            return null;
        }

        $current = Carbon::parse($this->date);
        $now = now();

        // If current date is in the past, find the next occurrence
        if ($current->lt($now)) {
            while ($current->lt($now)) {
                $current = $this->calculateNextOccurrence($current);

                // Check if we've exceeded end date
                if ($this->end_date && $current->gt($this->end_date)) {
                    return null;
                }
            }
        }

        return $current;
    }

    protected function calculateNextOccurrence(Carbon $current)
    {
        $next = $current->copy();

        switch ($this->recurrence_pattern) {
            case self::RECURRENCE_DAILY:
                $next->addDays($this->recurrence_interval);
                break;

            case self::RECURRENCE_WEEKLY:
                if (!empty($this->recurrence_weekdays)) {
                    $weekdays = $this->recurrence_weekdays;
                    $currentDay = strtolower($next->format('l'));

                    $currentIndex = array_search($currentDay, $weekdays);
                    if ($currentIndex !== false && isset($weekdays[$currentIndex + 1])) {
                        $nextDay = $weekdays[$currentIndex + 1];
                        $next->next(Carbon::parse($nextDay)->dayOfWeek);
                    } else {
                        $nextDay = $weekdays[0];
                        // FIX: Handle 0 interval - treat as 1 (every week)
                        $intervalWeeks = $this->recurrence_interval === 0 ? 1 : $this->recurrence_interval;
                        $next->addWeeks($intervalWeeks - 1)->next(Carbon::parse($nextDay)->dayOfWeek);
                    }
                } else {
                    // FIX: Handle 0 interval - treat as 1 (every week)
                    $intervalWeeks = $this->recurrence_interval === 0 ? 1 : $this->recurrence_interval;
                    $next->addWeeks($intervalWeeks);
                }
                break;

            case self::RECURRENCE_MONTHLY:
                $day = $next->day;
                $next->addMonths($this->recurrence_interval);
                $next->day = min($day, $next->daysInMonth);
                break;

            case self::RECURRENCE_YEARLY:
                $next->addYears($this->recurrence_interval);
                break;

            case self::RECURRENCE_WEEKDAYS:
                do {
                    $next->addDay();
                } while ($next->isWeekend());
                break;
        }

        return $next;
    }

    public static function getRecurrenceOptions()
    {
        return [
            self::RECURRENCE_NONE => 'Does not repeat',
            self::RECURRENCE_DAILY => 'Daily',
            self::RECURRENCE_WEEKLY => 'Weekly',
            self::RECURRENCE_MONTHLY => 'Monthly',
            self::RECURRENCE_YEARLY => 'Yearly',
            self::RECURRENCE_WEEKDAYS => 'Every weekday (Monday to Friday)',
        ];
    }
}
