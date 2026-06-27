<?php

namespace App\Helpers;

use App\Models\AttendanceSetting;

class AttendanceHelper
{
    public static function getSettings()
    {
        return AttendanceSetting::firstOrCreate([]);
    }

    public static function getStartTime()
    {
        return self::getSettings()->start_time;
    }

    public static function getEndTime()
    {
        return self::getSettings()->end_time;
    }

    public static function getDailyWorkingHours()
    {
        return self::getSettings()->daily_working_hours;
    }

    public static function getDefaultBreakMinutes()
    {
        return self::getSettings()->default_break_minutes;
    }

    public static function getOvertimeThreshold()
    {
        return self::getSettings()->overtime_threshold;
    }

    public static function getLateGraceMinutes()
    {
        return self::getSettings()->late_grace_minutes;
    }

    public static function getRecordsPerPage()
    {
        return self::getSettings()->records_per_page;
    }

    public static function decimalToHoursMinutes($decimalHours)
    {
        $decimalHours = is_numeric($decimalHours) ? $decimalHours : 0;
        $totalSeconds = round($decimalHours * 3600);
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;
        return sprintf("%dh %02dm %02ds", $hours, $minutes, $seconds);
    }

    public static function minutesToHoursMinutes($minutes)
    {
        $minutes = max(0, (int) $minutes);
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf("%dh %02dm 00s", $h, $m);
    }

    public static function formatDecimalToTime($decimalHours)
    {
        if (!is_numeric($decimalHours)) return '00:00';
        $hours = floor($decimalHours);
        $minutes = floor(($decimalHours - $hours) * 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
