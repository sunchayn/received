<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\UserCreated' => [
            'App\Listeners\CreateUserNotificationPreferences',
            'App\Listeners\CreateUserDefaultPlan',
        ],

        'App\Events\FilesUploaded' => [
            'App\Listeners\SendUploadedFilesNotification',
            'App\Listeners\RecalculateUserStorage',
        ],

        'App\Events\BucketUpdated' => [
            'App\Listeners\RecalculateUserStorage',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
