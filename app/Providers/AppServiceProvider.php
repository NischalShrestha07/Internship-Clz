<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['partials.header', 'partials.sidebar'], function ($view) {
            $user = Auth::user();

            if ($user) {
                $system_info = SystemSetting::first();
                $employee = Employee::where('employee_id', $user->id)->first();

                $today = Carbon::today('Asia/Kathmandu');
                $isClockedIn = Attendance::where('employee_id', $user->id)
                    ->whereDate('check_in', $today)
                    ->whereNull('check_out')
                    ->exists();

                $view->with([
                    'system_info' => $system_info,
                    'employee' => $employee,
                    'isClockedIn' => $isClockedIn,
                ]);
            } else {
                $view->with([
                    'system_info' => null,
                    'employee' => null,
                    'isClockedIn' => false,
                ]);
            }
        });
    }
}
