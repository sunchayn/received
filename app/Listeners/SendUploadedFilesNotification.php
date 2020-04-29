<?php

namespace App\Listeners;

use App\Events\FilesUploaded;
use App\Models\Notification;
use App\Repositories\NotificationRepository;

class SendUploadedFilesNotification
{
    /**
     * Create the event listener.
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
        $notification = $event->user->unreadNotificationsByType(Notification::TYPE_RECEIVED_FILES)->first();

        try {
            // If the user does not have a recent notification for the same folder
            // Create a new one.
            if ($notification && $notification->data->folder_id === $event->folder->id) {
                NotificationRepository::updateReceivedFilesNotificationWithNewData($notification, count($event->files));
            } else {
                // Otherwise, add to the sum of files shown in the recent unseen notification content.
                NotificationRepository::createForFilesUploaded($event->user, $event->folder, count($event->files));
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
