<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('settings.profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|max:2048',
        ]);

        $user->update($request->only(['name', 'email']));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('employee_images', 'public');

            if ($user->employee) {
                if ($user->employee->image) {
                    Storage::disk('public')->delete($user->employee->image);
                }
                $user->employee->update(['image' => $path]);
            } else {
                Employee::create([
                    'employee_id' => $user->id,
                    'image' => $path,
                ]);
            }
        }

        return redirect()->route('settings.profile')->with('status', 'Profile updated successfully.');
    }
}
