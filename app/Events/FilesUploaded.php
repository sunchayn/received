<?php

namespace App\Events;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FilesUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $folder;
    public $files;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param Folder $folder
     * @param array $files
     */
    public function __construct(User $user, Folder $folder, array $files)
    {
        $this->folder = $folder;
        $this->files = $files;
        $this->user = $user;
    }
}
