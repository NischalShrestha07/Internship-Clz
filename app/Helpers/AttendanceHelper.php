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
}
