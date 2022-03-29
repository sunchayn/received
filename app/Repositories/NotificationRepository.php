<?php

namespace App\Repositories;

use App\Models\Folder;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Class NotificationRepository.
 */
class NotificationRepository
{
    /**
     * Create a new notification for RECEIVED_FILES type.
     *
     * @param  User  $user
     * @param  Folder  $folder
     * @param  int  $numberOfFiles
     */
    public static function createForFilesUploaded(User $user, Folder $folder, int $numberOfFiles)
    {
        $data = [
            'folder_id' => $folder->id,
            'folder_name' => $folder->name,
            'files' => $numberOfFiles,
        ];

        $user->notifications()->create([
            'title' => 'Someone sent you new files.',
            'content' => self::craftContentForFiles($numberOfFiles, $folder->name),
            'data' => $data,
        ]);
    }

    /**
     * Update a previous unseen notification to accumulate more files to the notification content.
     *
     * @param $notification
     * @param  int  $numberOfFiles
     */
    public static function updateReceivedFilesNotificationWithNewData($notification, int $numberOfFiles)
    {
        $data = $notification->data;

        $data->files += $numberOfFiles;

        $notification->content = self::craftContentForFiles(
            $data->files,
            $notification->data->folder_name
        );

        $notification->data = $data;
        $notification->save();
    }

    /**
     * Create a notification content for uploaded files.
     *
     * @param  int  $numberOfFiles
     * @param  string  $folderName
     * @return string
     */
    public static function craftContentForFiles(int $numberOfFiles, string $folderName)
    {
        $wordCountability = $numberOfFiles > 0 ? 'files' : 'file';

        return 'You\'ve received '.$numberOfFiles.' new '.$wordCountability.' into "'.$folderName.'" folder.';
    }

    /**
     * Push the notification to users using another channels like ( SMS, Emails ).
     *
     * @param  string  $channel
     * @param  \Closure  $shouldSend
     * @param  \Closure  $delivery
     */
    public static function deliveryNotifications(string $channel, \Closure $shouldSend, \Closure $delivery)
    {
        $notificationsByUser = Notification::notNotified($channel)->with('user')->get()->groupBy('user_id');

        foreach ($notificationsByUser as $notifications) {
            /**
             * @var Collection $notifications
             * @var Notification $notification
             * @var User $user
             */
            $notification = $notifications->last();
            $user = $notification->user;

            if (! $shouldSend($user)) {
                continue;
            }

            $content = '';

            if ($notifications->count() > 1) {
                $content .= 'You have '.$notifications->count().' new updates. Last update: ';
            }

            $content .= $notification->content;

            $delivery($content, $user);
        }
    }
}
