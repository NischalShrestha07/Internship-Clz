<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances';
    protected $fillable = [
        'employee_id',
        'employee_name',
        'date',
        'check_in',
        'check_out',
        'type',
        'remarks',
        'comments',
        'status',
        'notes',
        'break',
        'overtime'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'break' => 'decimal:2',
        'overtime' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function punchLogs()
    {
        return $this->hasMany(PunchLog::class);
    }
    public function breaks()
    {
        return $this->hasMany(BreakRecord::class);
    }
    public function breakRecords(): HasMany
    {
        return $this->hasMany(BreakRecord::class);
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
