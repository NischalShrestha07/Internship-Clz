<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $system = SystemSetting::first();
        return view('settings.system-settings', compact('system'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'logo' => 'nullable|mimes:png|max:1024',
        ]);

        $system = SystemSetting::first();
        $logoPath = $system?->logo;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        if ($system) {
            $system->update([
                'name' => $request->name,
                'title' => $request->title,
                'logo' => $logoPath,
            ]);
        } else {
            SystemSetting::create([
                'name' => $request->name,
                'title' => $request->title,
                'logo' => $logoPath,
            ]);
        }

        return redirect()->route('settings.system')->with('status', 'System saved successfully!');
    }
}
