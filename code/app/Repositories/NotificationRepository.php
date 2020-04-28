<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Folder;

class NotificationRepository
{
    /**
     * Create a new notification for RECEIVED_FILES type
     *
     * @param User $user
     * @param Folder $folder
     * @param int $numberOfFiles
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
     * @param int $numberOfFiles
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

    public static function craftContentForFiles(int $numberOfFiles, string $folderName)
    {
        $wordCountability  = $numberOfFiles > 0 ? 'files' : 'file';
        return 'You\'ve received ' . $numberOfFiles . ' new '. $wordCountability .' into "' . $folderName . '" folder.';
    }
}
