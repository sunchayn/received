<?php

namespace App\Listeners;

use App\Events\FilesUploaded;
use App\Repositories\NotificationRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUploadedFilesNotification
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param FilesUploaded $event
     * @return void
     */
    public function handle(FilesUploaded $event)
    {
        NotificationRepository::createForFilesUploaded($event->user, $event->folder, $event->files);
    }
}
