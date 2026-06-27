<?php

namespace App\Models;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'description',
        'head_of_department',
        'is_active',
    ];

    public function head()
    {
        return $this->belongsTo(User::class, 'head_of_department');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
