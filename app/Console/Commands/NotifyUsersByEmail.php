<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\NotifMailDelivery;
use App\Repositories\NotificationRepository;
use Illuminate\Console\Command;

class NotifyUsersByEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users via Email channel.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('Notifying...');

        NotificationRepository::deliveryNotifications(
            Notification::EMAIL,
            function (User $user) {
                return $user->notificationPrefs->notify_by_mail && $user->email;
            },
            function (string $content, User $user) {
                $user->notify(new NotifMailDelivery($content));
                $user->unreadNotifications()->update(['is_notified_by_mail' => true]);
            }
        );

        $this->info('Done!');
    }
}
