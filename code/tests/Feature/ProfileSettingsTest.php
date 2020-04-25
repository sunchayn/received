<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Auth;
use App\Models\User;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @test
     */
    public function user_can_change_his_username()
    {
        $this->signin();

        $username = 'sunchayn';

        $data = [
            'username' => $username,
        ];

        $this
            ->patch(route('settings.username'), $data)
            ->assertOk()
        ;

        $this->assertEquals($username, Auth::user()->refresh()->username);
    }

    /**
     * @test
     */
    public function it_does_not_allow_duplicate_usernames()
    {
        $this->signin();

        $username = 'sunchayn';

        $data = [
            'username' => $username,
        ];

        // Another user with the same username
        // --
        factory(User::class)->create($data);

        $this
            ->json('patch', route('settings.username'), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'username',
            ])
        ;
    }

    /**
     *
     * @dataProvider valid_profile_data_provider
     * @test
     * @param $data
     */
    public function user_can_change_his_profile($data)
    {
        $this->signin();

        $this
            ->patch(route('settings.profile'), $data)
            ->assertOk()
        ;

        $this->assertNewDataIsPersisted($data, Auth::user()->refresh());
    }

    /**
     * @dataProvider invalid_profile_data_provider
     * @test
     * @param $data
     * @param $expectedErrors
     */
    public function it_does_not_allow_invalid_profile_data($data, $expectedErrors)
    {
        $this->signin();

        $this
            ->json('patch', route('settings.profile'), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors($expectedErrors)
        ;
    }

    /**
     * @test
     */
    public function it_does_not_allow_duplicate_emails()
    {
        $this->signin();

        $email = 'sunchayn@received.app';

        $data = [
            'email' => $email,
        ];

        // Another user with the same username
        // --
        factory(User::class)->create($data);

        $this
            ->json('patch', route('settings.profile'), $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ])
        ;
    }

    // Data Providers
    // --

    public function valid_profile_data_provider()
    {
        return [
            [
                [
                    'email' => 'test@received.app',
                    'name' => 'Mazen Touati',
                ],
            ],

            [
                [
                    'email' => 'test@received.app',
                ],
            ],

            [
                [
                    'name' => 'Mazen Touati',
                ],
            ],
        ];
    }

    public function invalid_profile_data_provider()
    {
        return [
            [
                [
                    'email' => 'testreceived.app',
                    'name' => 'Mazen Touati',
                ],

                [
                    'email',
                ]
            ],

            [
                [
                    'email' => 'test@received.app',
                    'name' => '000',
                ],

                [
                    'name',
                ]
            ],
        ];
    }
}
