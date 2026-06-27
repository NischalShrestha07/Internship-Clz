<div>
    <!-- Header Section -->
    <x-headings title="Attendance Tracker" />
    <x-notifications />
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-3 mx-auto">
        <x-attendance-tracker.stats-card title="Total Work Hours" value="{{ $todayStats['hours'] }}" icon="clock"
            color="blue" format="time" suffix=" Hrs" />

        <x-attendance-tracker.stats-card title="Weekly Hours" value="{{ $todayStats['week_hours'] }}"
            icon="calendar-week" color="green" format="time" suffix="/48 Hrs" />

        <x-attendance-tracker.stats-card title="Monthly Hours" value="{{ $todayStats['month_hours'] }}" icon="calendar"
            color="yellow" format="time" suffix="/192 Hrs" />
    </div>
    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-3">
        <!-- Time Tracking Card -->
        <x-attendance-tracker.time-tracking :isClockedIn="$isClockedIn" :isOnBreak="$isOnBreak"
            :currentNepalTime="$currentNepalTime" :totalHours="$totalHours" :todayStats="$todayStats"
            :breakTime="$breakTime" :overtimeToday="$overtimeToday" />
        <!-- Today's Timeline -->
        <x-attendance-tracker.timeline :todayActivities="$todayActivities" />
        <!-- Work Summary -->
        <x-attendance-tracker.work-summary :todayStats="$todayStats" />
    </div>

</div>