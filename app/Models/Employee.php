<?php

namespace App\Models;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable= [
        'employee_id',
        'department_id',
        'address',
        'gender',
        'date_of_birth',
        'phone_number',
        'joined_date',
        'basic_salary',
        'position',
        'CV_file',
        'citizenship_back',
        'citizenship_front',
        'image',
    ];
    protected $table="employees";
    public function user()
    {
        return $this->belongsTo(User::class,'employee_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}