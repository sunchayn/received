<?php

namespace App\Listeners;

use App\Events\BucketUpdated;
use App\Events\FilesUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

class RecalculateUserStorage
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
     * @param $event
     * @return void
     */
    public function handle($event)
    {
        if (!($event instanceof BucketUpdated || $event instanceof FilesUploaded)) {
            return;
        }

        if ($event->user->subscription) {
            $size = 0;

            $files = Storage::disk('buckets')->files($event->user->getBucket(), true);

            foreach ($files as $file) {
                // Add size in Kb
                $size += Storage::disk('buckets')->size($file) / 1024;
            }

            $event->user->subscription->update([
                'used_storage' => $size,
            ]);
        }
    }
}
