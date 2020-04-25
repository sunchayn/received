<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\NotificationsRequest;
use Auth;

class Notifications extends Controller
{
    /**
     * PATCH settings/notifications
     *
     *  Update user notifications preferences
     *
     * @param NotificationsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(NotificationsRequest $request)
    {
        $data = $request->validated();

        $prefs = Auth::user()->notificationPrefs;

        if (! $prefs) {
            Auth::user()->notificationPrefs()->create($data);
        } else {
            $prefs->update($data);
        }

        return $this->jsonSuccess('The notifications preferences has been updated.');
    }
}
