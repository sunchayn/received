<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class Notifications extends Controller
{
    /**
     * GET /notifications.
     *
     * Get all notification.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        return $this->jsonData(Auth::user()->notifications);
    }

    /**
     * GET /notifications/pull.
     *
     * Get unread notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pull()
    {
        return $this->jsonData(Auth::user()->unreadNotifications);
    }

    /**
     * PATCH /notifications/read.
     *
     * Mark the given notifications as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        $ids = request()->get('ids');
        Auth::user()->notifications()->whereIn('id', $ids)->update(['is_seen' => true]);

        return $this->empty();
    }
}
