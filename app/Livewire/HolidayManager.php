<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HolidayManager extends Component
{
    public $holidays = [];
    public $title;
    public $description;
    public $start_date;
    public $end_date;
    public $is_recurring = false;
    public $recurrence_pattern = 'yearly';
    public $is_active = true;
    public $editingId;
    public $showModal = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'is_recurring' => 'boolean',
        'recurrence_pattern' => 'required_if:is_recurring,true|in:yearly,monthly,weekly',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        $this->loadHolidays();
    }

    public function loadHolidays()
    {
        $this->holidays = Holiday::orderBy('start_date')->get();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function editHolidayModal($id)
    {
        $holiday = Holiday::findOrFail($id);
        $this->editingId = $id;
        $this->title = $holiday->title;
        $this->description = $holiday->description;
        $this->start_date = $holiday->start_date;
        $this->end_date = $holiday->end_date;

        $this->dispatch('open-modal', 'view-holiday');
    }

    public function deleteHolidayModal($id)
    {
        $holiday = Holiday::findOrFail($id);

        $this->dispatch('open-modal', 'delete-holiday');
    }

    public function saveHoliday()
    {
        $this->validate();

        $data = [
            'date' => '2000-02-12',
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_recurring' => $this->is_recurring,
            'recurrence_pattern' => $this->is_recurring ? $this->recurrence_pattern : null,
            'is_active' => $this->is_active,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            Holiday::find($this->editingId)->update($data);
        } else {
            Holiday::create($data);
        }

        $this->loadHolidays();
        $this->dispatch('close-modal', 'view-holiday');
        $this->dispatch('show-notification', 'Holiday ' . ($this->editingId ? 'updated' : 'added') . ' successfully!');
    }

    public function updateHoliday($id)
    {
        $holiday = Holiday::findOrFail($id);
        $this->editingId = $id;

        $this->title = $holiday->title;
        $this->description = $holiday->description;
        $this->start_date = $holiday->start_date->format('Y-m-d');
        $this->end_date = $holiday->end_date->format('Y-m-d');
        $this->is_recurring = $holiday->is_recurring;
        $this->recurrence_pattern = $holiday->recurrence_pattern ?? 'yearly';
        $this->is_active = $holiday->is_active;

        $this->showModal = true;
    }

    public function confirmDeleteHoliday($id)
    {
        Holiday::destroy($id);
        $this->loadHolidays();
        $this->dispatch('close-modal', 'delete-holiday');
        $this->dispatch('show-notification', 'Holiday deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'description',
            'start_date',
            'end_date',
            'is_recurring',
            'recurrence_pattern',
            'is_active',
            'editingId'
        ]);
    }

    public function render()
    {
        return view('livewire.holiday-manager')
            ->layout('components.layouts.app', ['title' => 'Holiday']);
    }
}
