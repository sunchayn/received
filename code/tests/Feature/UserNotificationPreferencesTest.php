<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\SMS\Provider as SMSProvider;
use App\Models\User;
use App\Models\NotificationPrefs;
use Auth;

class UserNotificationPreferencesTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        SMSProvider::setupFakeService([]);
    }

    /**
     * @test
     */
    public function it_create_user_notification_preferences_on_signup() {
        $data = factory(User::class)->make();

        $this
            ->post(route('auth.signup'), $data->toArray())
        ;

        $user = User::first();
        $this->assertNotNull($user->notificationPrefs);
    }

    /**
     * @dataProvider preferences_data_provider
     * @test
     * @param $data
     */
    public function user_can_update_his_notification_preferences($data) {
        $user = factory(User::class)->create();

        factory(NotificationPrefs::class)->create([
            'user_id' => $user->id,
        ]);

        $this->signin($user);

        $this
            ->json('patch', route('settings.notifications'), $data)
            ->assertOk()
        ;

        $this->assertNewDataIsPersisted($data, Auth::user()->notificationPrefs);
    }

    // Data providers
    // --

    public function preferences_data_provider() {
        return [
            [
                [
                    'notify_by_mail' => false,
                    'notify_by_sms' => true,
                ],
            ],
        ];
    }
}
