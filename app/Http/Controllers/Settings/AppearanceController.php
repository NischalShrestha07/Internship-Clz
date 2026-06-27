<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public function edit()
    {
        return view('settings.appearance');
    }

    public function update(Request $request)
    {
        return redirect()->route('settings.appearance')->with('status', 'Appearance updated.');
    }
}
