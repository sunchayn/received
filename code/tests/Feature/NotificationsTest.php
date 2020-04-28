<?php

namespace Tests\Feature;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Auth;
use App\Models\User;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_can_fetch_his_notifications()
    {
        $this->signin();

        $notificationsCount = 5;
        factory(Notification::class, $notificationsCount)->create([
            'user_id' => Auth::user(),
        ]);

        factory(Notification::class, 10)->create([
            'user_id' => factory(User::class)->create()->id,
        ]);

        $this->ajax('get', route('notifications.all'))
            ->assertOk()
            ->assertJsonCount($notificationsCount)
        ;
    }

    /**
     * @test
     */
    public function user_can_fetch_unread_notifications()
    {
        $this->signin();

        $notificationsCount = 5;

        factory(Notification::class, 3)->create([
            'user_id' => Auth::id(),
            'is_seen' => true,
        ]);

        factory(Notification::class, $notificationsCount)->create([
            'user_id' => Auth::id(),
            'is_seen' => false,
        ]);

        $this
            ->json('get', route('notifications.pull'))
            ->assertOk()
            ->assertJsonCount($notificationsCount)
        ;
    }

    /**
     * @test
     */
    public function it_mark_notifications_as_seen()
    {
        $this->signin();

        $notifications = factory(Notification::class, 5)->create([
            'user_id' => Auth::id(),
            'is_seen' => false,
        ]);

        $ids = [1, 3, 5];

        $this
            ->ajax('patch', route('notifications.read'), ['ids' => $ids])
            ->assertStatus(204)
        ;

        /**
         * @var Notification $notification
         */
        foreach ($notifications as $notification) {
            $notification->refresh();
            if (in_array($notification->id, $ids)) {
                $this->assertTrue($notification->isSeen());
            } else {
                $this->assertNotTrue($notification->isSeen());
            }
        }
    }
}