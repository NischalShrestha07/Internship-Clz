<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);
        $user->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    public function markRead($id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        if ($notification->notifiable_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return redirect()->back();
    }
}
