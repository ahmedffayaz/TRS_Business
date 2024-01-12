<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function clearAllNotifications()
    {
        $auth_user = auth()->user();
        foreach ($auth_user->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return redirect()->back();
    }
}
