@extends('components.layouts.app')

@section('content')
<x-headings title="Notifications" />

<div class="bg-white dark:bg-gray-800 rounded-lg p-6">
    @forelse($notifications as $notification)
    @php
    $data = $notification->data;
    if (isset($data['payroll_id'])) { $icon = 'receipt'; $iconBg = 'purple'; }
    elseif (isset($data['notice_id'])) { $icon = 'campaign'; $iconBg = 'orange'; }
    elseif (isset($data['type']) && $data['type'] === 'approved') { $icon = 'check_circle'; $iconBg = 'green'; }
    elseif (isset($data['type']) && $data['type'] === 'rejected') { $icon = 'cancel'; $iconBg = 'red'; }
    else { $icon = 'notifications'; $iconBg = 'blue'; }
    @endphp
    <div class="flex items-start gap-4 p-4 {{ $loop->first ? '' : 'border-t border-gray-200 dark:border-gray-700' }} {{ $notification->read_at ? 'opacity-75' : '' }}">
        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
            @if($iconBg === 'green') bg-green-100 dark:bg-green-900
            @elseif($iconBg === 'red') bg-red-100 dark:bg-red-900
            @elseif($iconBg === 'purple') bg-purple-100 dark:bg-purple-900
            @elseif($iconBg === 'orange') bg-orange-100 dark:bg-orange-900
            @else bg-blue-100 dark:bg-blue-900 @endif">
            <span class="material-symbols-outlined
                @if($iconBg === 'green') text-green-600 dark:text-green-300
                @elseif($iconBg === 'red') text-red-600 dark:text-red-300
                @elseif($iconBg === 'purple') text-purple-600 dark:text-purple-300
                @elseif($iconBg === 'orange') text-orange-600 dark:text-orange-300
                @else text-blue-600 dark:text-blue-300 @endif">{{ $icon }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm {{ $notification->read_at ? 'text-gray-600 dark:text-gray-400' : 'text-gray-900 dark:text-white font-semibold' }}">
                {{ $data['message'] ?? 'No message' }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
        </div>
        @if($notification->read_at)
        <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">Read</span>
        @else
        <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="flex-shrink-0">
            @csrf
            <button type="submit" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Mark read</button>
        </form>
        @endif
    </div>
    @empty
    <div class="text-center py-12">
        <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 mb-4">notifications_off</span>
        <p class="text-gray-500 dark:text-gray-400">No notifications yet</p>
    </div>
    @endforelse

    @if($notifications->hasPages())
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endSection
