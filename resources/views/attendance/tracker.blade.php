@extends('components.layouts.app')

@section('content')
<x-headings title="Attendance Tracker" />
<x-notifications />

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-3 mx-auto">
    <x-attendance-tracker.stats-card title="Total Today" value="{{ $todayStats['hours'] }}" icon="clock"
        color="blue" />
    <x-attendance-tracker.stats-card title="This Week" value="{{ $todayStats['week_hours'] }}/40h"
        icon="calendar-week" color="green" />
    <x-attendance-tracker.stats-card title="This Month" value="{{ $todayStats['month_hours'] }}/192h"
        icon="calendar" color="yellow" />
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-3">
    <x-attendance-tracker.time-tracking :isClockedIn="$isClockedIn" :isOnBreak="$isOnBreak"
        :currentNepalTime="$currentNepalTime" :totalHours="$totalHours" :todayStats="$todayStats"
        :breakTime="$breakTime" :overtimeToday="$overtimeToday" />
    <x-attendance-tracker.timeline :todayActivities="$todayActivities" />
    <x-attendance-tracker.work-summary :todayStats="$todayStats" />
</div>
@endsection
