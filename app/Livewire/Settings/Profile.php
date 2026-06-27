<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';

    public $phone_number;
    public $gender;
    public $address;
    public $image;
    public $date_of_birth;
    public $joined_date;
    public $imagePreview;
    public $citizenship_back;
    public $citizenship_front;
    public $CV_file;

    public $basic_salary;
    public $position;

    public $citizenship_back_preview;
    public $citizenship_front_preview;
    public $CV_file_preview;

    // Pending-clear flags (UI-only until save)
    public bool $pendingClearBack = false;
    public bool $pendingClearFront = false;
    public bool $pendingClearCv = false;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $employee = Auth::user()->employee;

        if ($employee) {
            $this->phone_number = $employee->phone_number;
            $this->gender = $employee->gender;
            $this->address = $employee->address;
            $this->date_of_birth = $employee->date_of_birth;
            $this->joined_date = $employee->joined_date;
            $this->imagePreview = $employee->image;
            $this->basic_salary = $employee->basic_salary;
            $this->position = $employee->position;
            $this->citizenship_back_preview = $employee->citizenship_back;
            $this->citizenship_front_preview = $employee->citizenship_front;
            $this->CV_file_preview = $employee->CV_file;
        }
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(Auth::id())],
            'address' => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'joined_date' => 'required|date|before_or_equal:today',
            'image' => 'nullable|image|max:1024',
            'citizenship_back' => 'nullable|image',
            'citizenship_front' => 'nullable|image',
            'CV_file' => 'nullable',
        ], [
            'phone_number.regex' => 'Phone number must be exactly 10 digits.',
        ]);

        $dob = \Carbon\Carbon::parse($this->date_of_birth);
        $joined = \Carbon\Carbon::parse($this->joined_date);

        if ($joined->lessThanOrEqualTo($dob)) {
            throw ValidationException::withMessages([
                'joined_date' => 'Joined date must be after the date of birth.',
            ]);
        }

        if ($dob->diffInYears($joined) < 18) {
            throw ValidationException::withMessages([
                'joined_date' => 'Employee must be at least 18 years old at the time of joining.',
            ]);
        }

        $user = Auth::user();
        $employee = $user->employee;

        $imagePath = $this->image ? $this->image->store('employees', 'public') : null;
        $citizenshipBackUploadPath = $this->citizenship_back ? $this->citizenship_back->store('citizenship', 'public') : null;
        $citizenshipFrontUploadPath = $this->citizenship_front ? $this->citizenship_front->store('citizenship', 'public') : null;
        $CVfilePath = $this->CV_file ? $this->CV_file->store('CVs', 'public') : null;

        
        $originalUserData = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $originalEmployeeData = $employee ? [
            'address' => $employee->address,
            'phone_number' => $employee->phone_number,
            'gender' => $employee->gender,
            'date_of_birth' => $employee->date_of_birth,
            'joined_date' => $employee->joined_date,
            'position' => $employee->position,
            'basic_salary' => $employee->basic_salary,
            'image' => $employee->image,
            'citizenship_back' => $employee->citizenship_back,
            'citizenship_front' => $employee->citizenship_front,
            'CV_file' => $employee->CV_file,
        ] : [];

       
        $newUserData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        // Image: prefer new upload, else keep existing
        $resolvedImage = $imagePath ?? ($employee->image ?? null);

        // Citizenship back: if pending clear, null; else if new upload use that; else existing
        if ($this->pendingClearBack) {
            $resolvedCitizenshipBack = null;
        } elseif ($citizenshipBackUploadPath) {
            $resolvedCitizenshipBack = $citizenshipBackUploadPath;
        } else {
            $resolvedCitizenshipBack = $employee->citizenship_back ?? null;
        }

        // Citizenship front: similar
        if ($this->pendingClearFront) {
            $resolvedCitizenshipFront = null;
        } elseif ($citizenshipFrontUploadPath) {
            $resolvedCitizenshipFront = $citizenshipFrontUploadPath;
        } else {
            $resolvedCitizenshipFront = $employee->citizenship_front ?? null;
        }

        // CV file
        if ($this->pendingClearCv) {
            $resolvedCV = null;
        } elseif ($CVfilePath) {
            $resolvedCV = $CVfilePath;
        } else {
            $resolvedCV = $employee->CV_file ?? null;
        }

        $newEmployeeData = [
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'joined_date' => $this->joined_date,
            'position' => $this->position,
            'basic_salary' => $this->basic_salary,
            'image' => $resolvedImage,
            'citizenship_back' => $resolvedCitizenshipBack,
            'citizenship_front' => $resolvedCitizenshipFront,
            'CV_file' => $resolvedCV,
        ];

        $changesMade = $newUserData != $originalUserData || $newEmployeeData != $originalEmployeeData;

        if (!$changesMade) {
            $this->dispatch('show-notification', 'Nothing was updated.');
            return;
        }

        DB::transaction(function () use ($user, $newUserData, $newEmployeeData, $employee) {
           
            $user->update($newUserData);

           
            if ($this->pendingClearBack && $employee && is_string($employee->citizenship_back) && $employee->citizenship_back !== '') {
                Storage::disk('public')->delete($employee->citizenship_back);
            }
            if ($this->pendingClearFront && $employee && is_string($employee->citizenship_front) && $employee->citizenship_front !== '') {
                Storage::disk('public')->delete($employee->citizenship_front);
            }
            if ($this->pendingClearCv && $employee && is_string($employee->CV_file) && $employee->CV_file !== '') {
                Storage::disk('public')->delete($employee->CV_file);
            }

            
            $user->employee()->updateOrCreate(
                ['employee_id' => $user->id],
                $newEmployeeData
            );
        });

        
        $this->pendingClearBack = false;
        $this->pendingClearFront = false;
        $this->pendingClearCv = false;

        
        $this->citizenship_back_preview = $newEmployeeData['citizenship_back'];
        $this->citizenship_front_preview = $newEmployeeData['citizenship_front'];
        $this->CV_file_preview = $newEmployeeData['CV_file'];

        
        $this->reset([ 'citizenship_back', 'citizenship_front', 'CV_file']);

        $this->redirectRoute('settings.profile', navigate: true);
        $this->dispatch('show-notification', 'Profile Updated Successfully');
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    
    public function clearCitizenshipBackPreviewOnly()
    {
        $this->pendingClearBack = true;
        $this->citizenship_back_preview = null;
        
    }

    public function clearCitizenshipFrontPreviewOnly()
    {
        $this->pendingClearFront = true;
        $this->citizenship_front_preview = null;
      
    }

    public function clearCvPreviewOnly()
    {
        $this->pendingClearCv = true;
        $this->CV_file_preview = null;
        
    }
}