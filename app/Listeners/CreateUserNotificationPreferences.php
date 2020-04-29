<?php

namespace App\Listeners;

use App\Events\UserCreated;

class CreateUserNotificationPreferences
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $event->user->notificationPrefs()->create([]);
    }
}
