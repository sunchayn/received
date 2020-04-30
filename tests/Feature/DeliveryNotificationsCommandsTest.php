<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\NotificationPrefs;
use App\Models\User;
use App\Services\SMS\Provider as SMSProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mail;
use Tests\TestCase;

class DeliveryNotificationsCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        SMSProvider::setupFakeService([]);
    }

    /**
     * @test
     */
    public function it_runs_scheduled_tasks()
    {
        $this->artisan('schedule:run')
            ->assertExitCode(0);
    }

    /**
     * @test
     */
    public function it_properly_send_sms_notification_to_users()
    {
        $users = factory(User::class, 3)->state('with_prefs')->create();

        NotificationPrefs::whereIn('user_id', $users->pluck('id'))->update([
            'notify_by_sms' => true,
        ]);

        foreach ($users as $user) {
            factory(Notification::class)->create([
                'user_id' => $user->id,
                'is_notified_by_sms' => false,
                'is_seen' => false,
            ]);
        }

        $this->artisan('notify:sms')
            ->assertExitCode(0);

        $deliveredNotifications = Notification::where('is_notified_by_sms', true)->whereIn('user_id', $users->pluck('id'))->count();

        $this->assertEquals($users->count(), $deliveredNotifications);
    }

    /**
     * @test
     */
    public function it_respect_users_sms_preferences()
    {
        $user = factory(User::class)->state('with_prefs')->create();

        $user->notificationPrefs->update([
            'notify_by_sms' => false,
        ]);

        $notification = factory(Notification::class)->create([
            'user_id' => $user->id,
            'is_notified_by_sms' => false,
            'is_seen' => false,
        ]);

        $this->artisan('notify:sms')
            ->assertExitCode(0);

        $this->assertFalse($notification->refresh()->is_notified_by_sms);
    }

    /**
     * @test
     */
    public function it_properly_send_mail_notification_to_users()
    {
        $users = factory(User::class, 3)->state('with_prefs')->create();

        NotificationPrefs::whereIn('user_id', $users->pluck('id'))->update([
            'notify_by_mail' => true,
        ]);

        foreach ($users as $user) {
            factory(Notification::class)->create([
                'user_id' => $user->id,
                'is_notified_by_mail' => false,
                'is_seen' => false,
            ]);
        }

        $this->artisan('notify:mail')
            ->assertExitCode(0);

        $deliveredNotifications = Notification::where('is_notified_by_mail', true)->whereIn('user_id', $users->pluck('id'))->count();

        $this->assertEquals($users->count(), $deliveredNotifications);
    }

    /**
     * @test
     */
    public function it_respect_users_mail_preferences()
    {
        $user = factory(User::class)->state('with_prefs')->create();

        $user->notificationPrefs->update([
            'notify_by_mail' => false,
        ]);

        $notification = factory(Notification::class)->create([
            'user_id' => $user->id,
            'is_notified_by_mail' => false,
            'is_seen' => false,
        ]);

        $this->artisan('notify:sms')
            ->assertExitCode(0);

        $this->assertFalse($notification->refresh()->is_notified_by_mail);
    }
}
