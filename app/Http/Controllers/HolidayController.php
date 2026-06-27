<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('start_date', 'desc')->paginate(20);
        return view('holiday-manager', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'required_if:is_recurring,true|in:yearly,monthly,weekly',
            'is_active' => 'boolean',
        ]);

        Holiday::create($request->all());

        return redirect()->route('holiday.manager')->with('status', 'Holiday added successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holiday.manager')->with('status', 'Holiday deleted successfully.');
    }
}
