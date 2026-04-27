<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->count();

        // Mark all as read when page is viewed
        Notification::where('user_id', Auth::id())
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Notification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->markAsRead();
        return back();
    }

    public function destroy(Notification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}