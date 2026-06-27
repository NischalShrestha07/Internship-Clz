@extends('components.layouts.app')

@section('content')
<x-headings title="Departments" />
<x-notifications />

@role('HR Manager')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Departments</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">With Head</p>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['has_head'] }}</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
    <div class="mb-6 p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Create New Department</h3>
        <hr class="w-full border-gray-300 dark:border-gray-600 mt-1 mb-5" />

        <form method="POST" action="{{ route('departments.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                <input type="text" name="name" placeholder="Department name" value="{{ old('name') }}" required
                    class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-4 py-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Head of Department</label>
                <select name="head_of_department"
                    class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Head</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('head_of_department') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                    @endforeach
                </select>
                @error('head_of_department')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
                <textarea name="description" rows="3" placeholder="Optional description..."
                    class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-4 py-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-800">Create Department</button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg p-6">
    <div class="overflow-x-auto rounded-lg">
        <div class="max-h-[calc(100vh-300px)] md:max-h-[calc(100vh-280px)]">
            <table class="w-full table-auto text-sm text-center shadow-xl border border-gray-300 dark:border-gray-700">
                <thead class="bg-blue-600 dark:bg-blue-900 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-4 font-semibold">Name</th>
                        <th class="px-4 py-4 font-semibold">Head of Department</th>
                        <th class="px-4 py-4 font-semibold">Employees</th>
                        <th class="px-4 py-4 font-semibold">Status</th>
                        <th class="px-4 py-4 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-700 text-black dark:text-white">
                    @forelse($departments as $department)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $department->name }}</td>
                        <td class="px-4 py-3">{{ $department->head->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $department->employees_count }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $department->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100' }}">
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">
                                <button type="button" onclick="editDepartment({{ $department->id }}, '{{ $department->name }}', '{{ $department->head_of_department }}', '{{ $department->description }}', {{ $department->is_active ? 'true' : 'false' }})"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-xs">Edit</button>
                                <button type="button" onclick="confirmDelete({{ $department->id }}, '{{ $department->name }}')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-xs">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No departments found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex justify-center sm:justify-end">
        {{ $departments->links() }}
    </div>
</div>

<x-modal name="edit-department-modal">
    <div class="fixed inset-0 bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[500px] items-center p-6 relative mx-2">
            <button x-on:click.prevent="$dispatch('close')"
                class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">&times;</button>
            <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-white">Edit Department</h2>
            <form id="editDepartmentForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                        <input type="text" name="name" id="edit_name" required
                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Head of Department</label>
                        <select name="head_of_department" id="edit_head_of_department"
                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Head</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
                        <textarea name="description" id="edit_description" rows="3"
                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500" />
                        <label for="edit_is_active" class="text-sm font-medium text-gray-700 dark:text-gray-200">Active</label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">Update Department</button>
                    <button type="button" x-on:click.prevent="$dispatch('close')" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</x-modal>

<x-modal name="delete-department-modal">
    <div class="fixed inset-0 bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[500px] items-center p-6 relative mx-2">
            <button x-on:click.prevent="$dispatch('close')"
                class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">&times;</button>
            <h2 class="text-2xl font-semibold mb-4 text-center text-black dark:text-white">Delete Department</h2>
            <p class="text-center text-gray-700 dark:text-white text-lg mb-6">Are you sure you want to delete <span id="deleteDepartmentName" class="font-semibold"></span>?</p>
            <form id="deleteDepartmentForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600">Delete</button>
                    <button type="button" x-on:click.prevent="$dispatch('close')" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</x-modal>

@php
$editBaseUrl = route('departments.update', 0);
$deleteBaseUrl = route('departments.destroy', 0);
@endphp
<script>
function editDepartment(id, name, headId, description, isActive) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_head_of_department').value = headId || '';
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('editDepartmentForm').action = '{{ $editBaseUrl }}'.replace(/\/0$/, '/' + id);
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-department-modal' }));
}

function confirmDelete(id, name) {
    document.getElementById('deleteDepartmentName').textContent = name;
    document.getElementById('deleteDepartmentForm').action = '{{ $deleteBaseUrl }}'.replace(/\/0$/, '/' + id);
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-department-modal' }));
}
</script>
@endrole
@endsection
