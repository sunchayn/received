<?php

namespace Tests\Feature;

use App;
use App\Models\User;
use App\Services\SMS\Provider as SMSProvider;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        SMSProvider::setupFakeService([]);
    }

    /**
     * @dataProvider phone_changing_data_provider
     * @param $data
     * @test
     */
    public function user_can_change_his_phone_number($data)
    {
        $this->signin();

        // It accepts the request
        // --
        $response = $this
            ->ajax('post', route('settings.change_phone'), $data)
            ->assertOk();

        $verificationRoute = route('settings.verify_phone', [
            'verification_id' => Auth::user()->verification_id,
        ]);

        // It returns a proper verification route
        // --
        $response->assertJsonFragment([
            'verification_route' => $verificationRoute,
        ]);

        // It shows error when code is wrong
        // --
        App::forgetInstance('SMS');
        SMSProvider::setupFakeService([
            'verification_should_succeed' => false,
        ]);

        $code = [
            'code' => 123456,
        ];

        $this
            ->ajax('post', $verificationRoute, $code)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code']);

        // It properly updates the new phone number upon verification
        // --
        App::forgetInstance('SMS');

        SMSProvider::setupFakeService([
            'verification_should_succeed' => true,
        ]);

        $code = [
            'code' => 123456,
        ];

        $this
            ->ajax('post', $verificationRoute, $code)
            ->assertOk();

        $this->assertNull(Auth::user()->refresh()->ongoingNewPhoneVerification);
        $this->assertNewDataIsPersisted($data, Auth::user());
    }

    /**
     * @dataProvider phone_changing_data_provider
     * @param $data
     * @test
     */
    public function it_does_not_allow_used_phones($data)
    {
        $this->signin();

        // Create a user with the same data
        // --
        factory(User::class)->create($data);

        $this
            ->ajax('post', route('settings.change_phone'), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'phone_number',
            ]);
    }

    /**
     * @param $data
     * @return void
     * @dataProvider valid_password_data_provider
     * @test
     */
    public function user_can_change_his_password($data)
    {
        $this->signin();

        Auth::user()->update([
            'password' => bcrypt($data['current']),
        ]);

        $this
            ->json('patch', route('settings.password'), $data)
            ->assertStatus(200);

        Auth::user()->refresh();
        $this->assertTrue(\Hash::check($data['password'], Auth::user()->getAuthPassword()));
    }

    /**
     * @param $isCurrentValid
     * @param $data
     * @param $fields
     * @return void
     * @dataProvider invalid_password_data_provider
     * @test
     */
    public function users_cant_update_their_password_when_invalid($isCurrentValid, $data, $fields)
    {
        $this->signin();

        $currentPassword = $data['password'].'.salt';

        Auth::user()->update([
            'password' => bcrypt($isCurrentValid ? $data['current'] : $data['current'].'salt'),
        ]);

        $response = $this->json('patch', route('settings.password'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($fields);
    }

    // Data providers
    // --

    public function phone_changing_data_provider()
    {
        return [
            [
                [
                    'phone_number' => '99999999',
                    'country_code' => 216,
                ],
            ],
        ];
    }

    public function valid_password_data_provider()
    {
        return [
            [
                [
                    'current' => '321654',
                    'password' => '123456',
                    'password_confirmation' => '123456',
                ],
            ],
        ];
    }

    public function invalid_password_data_provider()
    {
        return [
            [
                true, // Is current password valid?
                [
                    'current' => '321654',
                    'password' => '123456',
                    'password_confirmation' => '12',
                ],
                [
                    'password_confirmation',
                ],
            ],
            [
                false, // Is current password valid?
                [
                    'current' => '321654',
                    'password' => '123456',
                    'password_confirmation' => '12',
                ],
                [
                    'current', // It returns immediately when current is wrong.
                ],
            ],
        ];
    }
}
