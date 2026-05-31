<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        try {
            $notifications = DatabaseNotification::query()
                ->where('notifiable_type', get_class($user))
                ->where('notifiable_id', (string) $user->getKey())
                ->latest()
                ->paginate(20);
        } catch (\Throwable $throwable) {
            $notifications = new LengthAwarePaginator(collect(), 0, 20);
        }

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, $id)
    {
        $user = Auth::user();
        try {
            $notification = DatabaseNotification::query()
                ->where('id', $id)
                ->where('notifiable_type', get_class($user))
                ->where('notifiable_id', (string) $user->getKey())
                ->first();

            if ($notification) {
                $notification->markAsRead();
            }
        } catch (\Throwable $throwable) {
            // No-op fallback when notification storage is unavailable in local dev.
        }

        return back();
    }
}
