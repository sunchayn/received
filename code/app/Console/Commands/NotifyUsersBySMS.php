<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\NotifMailDelivery;
use App\Repositories\NotificationRepository;
use App\Services\SMS\ProviderInterface as SMSProviderInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class NotifyUsersBySMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users via SMS channel.';

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
     * Send notifications to users via SMS channel.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $this->line('Notifying...');

        /**
         * @var SMSProviderInterface $smsProvider
         */
        $smsProvider = app()->make('SMS');

        NotificationRepository::deliveryNotifications(
            Notification::SMS,
            function (User $user) {
                return $user->notificationPrefs->notify_by_sms;
            },
            function (string $content, User $user) use ($smsProvider) {
                if ($smsProvider->sendSMS($user, $content)) {
                    $user->unreadNotifications()->update([
                        'is_notified_by_sms' => true,
                    ]);
                }
            }
        );

        $this->info('Done!');
    }
}
