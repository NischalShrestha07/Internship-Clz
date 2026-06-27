<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('head')
            ->withCount('employees')
            ->orderBy('name')
            ->paginate(10);

        $employees = User::all();

        $stats = [
            'total' => Department::count(),
            'active' => Department::where('is_active', true)->count(),
            'has_head' => Department::whereNotNull('head_of_department')->count(),
        ];

        return view('hr.departments', compact('departments', 'employees', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
            'head_of_department' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')->with('status', 'Department created successfully.');
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string',
            'head_of_department' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')->with('status', 'Department updated successfully.');
    }

    public function destroy($id)
    {
        $department = Department::withCount('employees')->findOrFail($id);

        if ($department->employees_count > 0) {
            return redirect()->route('departments.index')->with('error', 'Cannot delete department with assigned employees.');
        }

        $department->delete();

        return redirect()->route('departments.index')->with('status', 'Department deleted successfully.');
    }
}
