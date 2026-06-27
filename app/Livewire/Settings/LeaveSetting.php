<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveConfigure;

class LeaveSetting extends Component
{
   public $leave_type;
   public $selectedLeaveType = '';
    public $default_days;
    public $leaveTypes = [];
    public $employees = [];
    public $employee ='';
    public $days = '';
    public string $pageTitle = 'Leave Setting ';

    
    public function mount()
    {
        $this->leaveTypes = LeaveType::all();
        $this->employees = User::role('employee')->get();
    }
    public function addLeaveType()
    {
         $this->validate([
             'leave_type' => 'required|unique:leave_types,leave_type',
             'default_days' => 'required|numeric',
         ]);
         LeaveType::create([
            'leave_type' => $this->leave_type,
            'default_days'=> $this->default_days,
         ]);
         $this->redirectRoute('setting.leave', navigate: true);
         $this->dispatch('show-notification','Leave Type added successfully');
    }
    public function submit()
    {
         $this->validate([
            'employee' =>'required|exists:users,id',
             'selectedLeaveType' => 'required|exists:leave_types,id',
             'days' => 'required|numeric',
         ]);
         $exists = EmployeeLeaveConfigure::where('employee_id', $this->employee)
        ->where('leave_type_id', $this->selectedLeaveType)
        ->exists();

        if ($exists) {
        $this->dispatch('show-error', 'This leave type configuration already exists for the employee.');
        return; 
        }
        EmployeeLeaveConfigure::create([
            'employee_id' => $this->employee,
            'leave_type_id' => $this->selectedLeaveType,
            'days'=> $this->days,
         ]);
         $this->redirectRoute('setting.leave', navigate: true);
         $this->dispatch('show-notification','Employee Leave Configuration added successfully');
    }
    public function render()
    {
        return view('livewire.settings.leave-setting')->layout('components.layouts.app', ['title' => $this->pageTitle]);
    }
  
    public function updatedSelectedLeaveType($value)
    {
        $leave = LeaveType::find($value); 
        if ($leave) {
            $this->days = $leave->default_days;
        } else {
            $this->days = '';
        }
    }
}