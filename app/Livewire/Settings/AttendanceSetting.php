<?php

namespace App\Livewire\Settings;

use App\Models\AttendanceSetting as ModelsAttendanceSetting;
use Livewire\Component;

class AttendanceSetting extends Component
{
    public $start_time;
    public $end_time;
    public $daily_working_hours;
    public $default_break_minutes;
    public $overtime_threshold;
    public $late_grace_minutes;
    public $records_per_page;

    public function mount()
    {
        $settings = ModelsAttendanceSetting::getSettings();

        $this->fill([
            'start_time' => $settings->start_time->format('H:i'),
            'end_time' => $settings->end_time->format('H:i'),
            'daily_working_hours' => $settings->daily_working_hours,
            'default_break_minutes' => $settings->default_break_minutes,
            'overtime_threshold' => $settings->overtime_threshold,
            'late_grace_minutes' => $settings->late_grace_minutes,
            'records_per_page' => $settings->records_per_page,
        ]);
    }

    public function submit()
    {
        $this->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'daily_working_hours' => 'required|numeric|min:1|max:24',
            'default_break_minutes' => 'required|integer|min:0',
            'overtime_threshold' => 'required|numeric|min:0',
            'late_grace_minutes' => 'required|integer|min:0',
            'records_per_page' => 'required|integer|min:5|max:100',
        ]);

        $settings = ModelsAttendanceSetting::getSettings();
        $settings->update([
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'daily_working_hours' => $this->daily_working_hours,
            'default_break_minutes' => $this->default_break_minutes,
            'overtime_threshold' => $this->overtime_threshold,
            'late_grace_minutes' => $this->late_grace_minutes,
            'records_per_page' => $this->records_per_page,
        ]);

        $this->dispatch(
            'show-notification',
            'Attendance settings updated successfully!'
        );
    }

    public function render()
    {
        return view('livewire.settings.attendance-setting')->layout('components.layouts.app', ['title' => 'Attendance Settings']);
    }
}
