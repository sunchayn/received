<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class FilesUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $files;

    public $user;

    /**
     * Create a new event instance.
     *
     * @param array $files
     * @param User $user
     */
    public function __construct(array $files, User $user)
    {
        $this->files = $files;
        $this->user = $user;
    }
}
