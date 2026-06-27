@extends('components.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Employee Details</h1>
        <a href="{{ route('hr.employees') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">&larr; Back to Employee List</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                <div class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 shadow overflow-hidden flex-shrink-0">
                    @if($user->employee && $user->employee->image)
                    <img src="{{ asset('storage/' . $user->employee->image) }}" class="w-full h-full object-cover" />
                    @else
                    <img src="{{ asset('default.jpg') }}" class="w-full h-full object-cover" />
                    @endif
                </div>
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Position</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ $user->employee->position ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="text-lg text-gray-900 dark:text-white mt-1">{{ $user->employee->phone_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date of Birth</p>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $user->employee->date_of_birth ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Gender</p>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $user->employee->gender ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</p>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $user->employee->address ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Joined Date</p>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $user->employee->joined_date ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Role</p>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $user->getRoleNames()->first() ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Salary</p>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $user->employee->basic_salary ? 'Rs. ' . number_format($user->employee->basic_salary) : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Documents</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Citizenship Front</p>
                    </div>
                    <div class="h-48 flex items-center justify-center p-4 text-center">
                        @if($user->employee && $user->employee->citizenship_front)
                            @if(str_ends_with($user->employee->citizenship_front, '.pdf'))
                            <p class="text-sm text-gray-500 dark:text-gray-400">PDF Document</p>
                            @else
                            <img src="{{ asset('storage/' . $user->employee->citizenship_front) }}" class="max-h-full max-w-full object-contain" alt="Citizenship Front" />
                            @endif
                        @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No document</p>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Citizenship Back</p>
                    </div>
                    <div class="h-48 flex items-center justify-center p-4 text-center">
                        @if($user->employee && $user->employee->citizenship_back)
                            @if(str_ends_with($user->employee->citizenship_back, '.pdf'))
                            <p class="text-sm text-gray-500 dark:text-gray-400">PDF Document</p>
                            @else
                            <img src="{{ asset('storage/' . $user->employee->citizenship_back) }}" class="max-h-full max-w-full object-contain" alt="Citizenship Back" />
                            @endif
                        @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No document</p>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">CV / Resume</p>
                    </div>
                    <div class="h-48 flex items-center justify-center p-4 text-center">
                        @if($user->employee && $user->employee->CV_file)
                            @if(str_ends_with($user->employee->CV_file, '.pdf'))
                            <iframe src="{{ asset('storage/' . $user->employee->CV_file) }}" class="w-full h-full rounded" frameborder="0"></iframe>
                            @else
                            <img src="{{ asset('storage/' . $user->employee->CV_file) }}" class="max-h-full max-w-full object-contain rounded" alt="CV" />
                            @endif
                        @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No document</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
