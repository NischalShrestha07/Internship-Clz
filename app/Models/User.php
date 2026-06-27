<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{

    use HasFactory, Notifiable, HasRoles;

    //    public function canAccessPanel(Panel $panel): bool
    //     {
    //         return $this->hasRole('Admin');
    //     }
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        \Log::info('canAccessPanel check', [
            'user_id' => $this->id,
            'roles' => $this->roles->pluck('name')->toArray(),
            'panel_id' => $panel->getId(),
        ]);
        return $this->hasAnyRole(['Admin', 'Employee']);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    public function Leave()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class, 'employee_id');
    }
    public function employeeLeaveConfigure()
    {
        return $this->hasMany(EmployeeLeaveConfigure::class, 'employee_id');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }
}
