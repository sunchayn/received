<?php

namespace App\Console\Commands;

use App\Models\Notification;
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

        $userNotifs = Notification::notNotified()->with('user')->get()->groupBy('user_id');

        foreach ($userNotifs as $notifications) {
            /**
             * @var Collection $notifications
             * @var Notification $notification
             */
            $notification = $notifications->last();

            if (! $notification->user->notificationPrefs->notify_by_sms) {
                continue;
            }

            $content = '';

            if ($notifications->count() > 1) {
                $content .= 'You have ' . $notifications->count() . ' new updates. Last update: ';
            }

            $content .= $notification->content;

            /**
             * @var SMSProviderInterface $smsProvider
             */
            $smsProvider = app()->make('SMS');

            if ($smsProvider->sendSMS($notification->user, $content)) {
                $notification->user->unreadNotifications()->update([
                    'is_notified' => true,
                ]);
            }
        }

        $this->info('Done!');
    }
}
