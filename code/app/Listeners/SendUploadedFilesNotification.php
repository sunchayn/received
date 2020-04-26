<?php

namespace App\Listeners;

use App\Events\FilesUploaded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUploadedFilesNotification
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
     * @param  FilesUploaded  $event
     * @return void
     */
    public function handle(FilesUploaded $event)
    {
        //
    }
}
