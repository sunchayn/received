<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Folder;

class NotificationRepository
{
    public static function createForFilesUploaded(User $user, Folder $folder, $files)
    {
        $user->notifications()->create([
            'title' => 'Someone sent you new files.',
            'content' => 'You got ' . count($files) . ' new files into your "' . $folder->name . '" folder',
        ]);
    }
}
