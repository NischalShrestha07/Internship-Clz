@extends('components.layouts.app')

@section('content')
<x-headings title="Notice Board" />
<x-notifications />

@role('HR Manager')
<div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6" x-data="{ showForm: false }">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Notices</h3>
        <button @click="showForm = !showForm"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            New Notice
        </button>
    </div>

    <div x-show="showForm" x-collapse class="mb-6 p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-4">Create New Notice</h4>
        <form method="POST" action="{{ route('notices.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Send To</label>
                <select name="employee_id"
                    class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Employees</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
                @error('employee_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notice Type</label>
                    <select name="notice_type" required
                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Announcement">Announcement</option>
                        <option value="Warning">Warning</option>
                        <option value="Reminder">Reminder</option>
                        <option value="Policy Update">Policy Update</option>
                        <option value="Meeting">Meeting</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Priority</label>
                    <select name="priority" required
                        class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Message</label>
                <textarea name="message" rows="4" placeholder="Enter notice message..." required
                    class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-4 py-3 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('message') }}</textarea>
                @error('message')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="showForm = false"
                    class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white">Cancel</button>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-800">Send Notice</button>
            </div>
        </form>
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('notices.index') }}">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search notices..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                @if(request('search'))
                <a href="{{ route('notices.index') }}" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 font-semibold">Employee</th>
                    <th class="px-4 py-3 font-semibold">Type</th>
                    <th class="px-4 py-3 font-semibold">Priority</th>
                    <th class="px-4 py-3 font-semibold">Message</th>
                    <th class="px-4 py-3 font-semibold">Issued By</th>
                    <th class="px-4 py-3 font-semibold">Date</th>
                    <th class="px-4 py-3 font-semibold text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                @forelse($notices as $notice)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xs font-bold">
                                {{ substr($notice->user?->name ?? 'All', 0, 2) }}
                            </div>
                            <span class="text-sm font-medium">{{ $notice->user->name ?? 'All Employees' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $notice->notice_type === 'Warning' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                            {{ $notice->notice_type === 'Announcement' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : '' }}
                            {{ $notice->notice_type === 'Reminder' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                            {{ $notice->notice_type === 'Policy Update' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                            {{ $notice->notice_type === 'Meeting' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                            {{ $notice->notice_type === 'General' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                            {{ $notice->notice_type }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $notice->priority === 'Urgent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                            {{ $notice->priority === 'High' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                            {{ $notice->priority === 'Medium' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                            {{ $notice->priority === 'Low' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' : '' }}">
                            {{ $notice->priority }}
                        </span>
                    </td>
                    <td class="px-4 py-3 max-w-xs">
                        <p class="truncate text-sm">{{ $notice->message }}</p>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $notice->issuer->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $notice->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex justify-center gap-2">
                            <button type="button" onclick="editNotice({{ $notice->id }}, '{{ $notice->employee_id ?? 'all' }}', '{{ $notice->notice_type }}', '{{ $notice->priority }}', '{{ addslashes($notice->message) }}')"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded transition-colors" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button type="button" onclick="confirmDelete({{ $notice->id }})"
                                class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>No notices found</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="space-y-4">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">My Notices</h3>

    @php
    $unreadCount = $notices->where('is_read', false)->count();
    @endphp

    @if($unreadCount > 0)
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        <span>You have <strong>{{ $unreadCount }}</strong> unread notice{{ $unreadCount > 1 ? 's' : '' }}.</span>
    </div>
    @endif

    @forelse($notices as $notice)
    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg transition-colors
        {{ $notice->is_read ? 'bg-white dark:bg-gray-800' : 'bg-blue-50/50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800' }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    @if(!$notice->is_read)
                    <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" title="Unread"></span>
                    @endif
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                        {{ $notice->notice_type === 'Warning' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                        {{ $notice->notice_type === 'Announcement' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : '' }}
                        {{ $notice->notice_type === 'Reminder' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                        {{ $notice->notice_type === 'Policy Update' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                        {{ $notice->notice_type === 'Meeting' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                        {{ $notice->notice_type === 'General' ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                        {{ $notice->notice_type }}
                    </span>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                        {{ $notice->priority === 'Urgent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : '' }}
                        {{ $notice->priority === 'High' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                        {{ $notice->priority === 'Medium' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                        {{ $notice->priority === 'Low' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' : '' }}">
                        {{ $notice->priority }}
                    </span>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $notice->message }}</p>
                <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <span>Issued by: {{ $notice->issuer->name ?? 'N/A' }}</span>
                    <span>{{ $notice->created_at->format('M d, Y \a\t h:i A') }}</span>
                    @if($notice->read_at)
                    <span class="text-green-600 dark:text-green-400">Read {{ $notice->read_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            @if(!$notice->is_read)
            <form method="POST" action="{{ route('notices.mark-read', $notice->id) }}">
                @csrf
                <button type="submit"
                    class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 whitespace-nowrap">
                    Mark Read
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-12">
        <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400">No notices for you.</p>
    </div>
    @endforelse
</div>
@endrole

<div class="mt-4">
    {{ $notices->appends(request()->query())->links() }}
</div>

@role('HR Manager')
<x-modal name="edit-notice-modal">
    <div class="fixed inset-0 bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[500px] items-center p-6 relative mx-2">
            <button x-on:click.prevent="$dispatch('close')"
                class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">&times;</button>
            <h2 class="text-xl font-semibold mb-4 text-center text-black dark:text-white">Edit Notice</h2>
            <form id="editNoticeForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Send To</label>
                        <select name="employee_id" id="edit_employee_id" required
                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">All Employees</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type</label>
                            <select name="notice_type" id="edit_notice_type" required
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Announcement">Announcement</option>
                                <option value="Warning">Warning</option>
                                <option value="Reminder">Reminder</option>
                                <option value="Policy Update">Policy Update</option>
                                <option value="Meeting">Meeting</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Priority</label>
                            <select name="priority" id="edit_priority" required
                                class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Message</label>
                        <textarea name="message" id="edit_message" rows="4" required
                            class="w-full mt-1 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">Update Notice</button>
                    <button type="button" x-on:click.prevent="$dispatch('close')" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</x-modal>

<x-modal name="delete-notice-modal">
    <div class="fixed inset-0 bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-[500px] items-center p-6 relative mx-2">
            <button x-on:click.prevent="$dispatch('close')"
                class="absolute top-2 right-2 text-red-500 hover:bg-red-500 hover:text-white text-xl font-bold rounded w-8 h-8 flex items-center justify-center">&times;</button>
            <h2 class="text-xl font-semibold mb-4 text-center text-black dark:text-white">Delete Notice</h2>
            <p class="text-center text-gray-700 dark:text-white text-base mb-6">Are you sure you want to delete this notice?</p>
            <form id="deleteNoticeForm" method="POST" action="">
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
$editBaseUrl = route('notices.update', 0);
$deleteBaseUrl = route('notices.destroy', 0);
@endphp
<script>
function editNotice(id, employeeId, noticeType, priority, message) {
    document.getElementById('edit_employee_id').value = employeeId;
    document.getElementById('edit_notice_type').value = noticeType;
    document.getElementById('edit_priority').value = priority;
    document.getElementById('edit_message').value = message;
    document.getElementById('editNoticeForm').action = '{{ $editBaseUrl }}'.replace(/\/0$/, '/' + id);
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-notice-modal' }));
}

function confirmDelete(id) {
    document.getElementById('deleteNoticeForm').action = '{{ $deleteBaseUrl }}'.replace(/\/0$/, '/' + id);
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'delete-notice-modal' }));
}
</script>
@endrole
@endsection
