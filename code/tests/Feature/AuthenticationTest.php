<?php
namespace Tests\Feature;

use Auth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider valid_user_data
     * @param $data
     * @test
     */
    public function user_can_signup($data)
    {
        $this
            ->post(route('auth.signup'), $data)
            ->assertStatus(302)
        ;

        // Session is created
        $this->assertTrue(Auth::check());
    }

    /**
     * @dataProvider valid_user_data
     * @param $data
     * @test
     */
    public function it_redirect_to_proper_verification_url_after_signup($data)
    {
        $response = $this
            ->post(route('auth.signup'), $data)
            ->assertStatus(302)
        ;

        $redirectRoute = route('auth.verify', ['verification_id' => Auth::user()->verification_id]);
        $response->assertRedirect($redirectRoute);
    }

    /**
     * @dataProvider invalid_user_data
     * @param $data
     * @param $expectedErrors
     * @test
     */
    public function on_signup_error_it_return_proper_response($data, $expectedErrors)
    {
        // When a request is not AJAX
        // it should redirect to Sign up page (Request came from Landing page).
        $this
            ->post(route('auth.signup'), $data)
            ->assertRedirect(route('auth.signup'))
            ->assertSessionHasErrors($expectedErrors)
        ;

        // When it's AJAX request
        // it should return errors in JSON format.
        $this
            ->ajax('post', route('auth.signup'), $data)
            ->assertJsonValidationErrors($expectedErrors)
        ;
    }

    /**
     * @test
     */
    public function it_does_not_create_account_twice()
    {
        $user = factory(User::class)->create();

        $data = [
            'phone_number' => $user->phone_number,
            'password' =>  123456,
        ];

        $this
            ->ajax('post', route('auth.signup'), $data)
            ->assertJsonValidationErrors([
                'phone_number',
            ])
        ;
    }

    /**
     * @test
     */
    public function user_can_login()
    {
        $password = '123456';

        // When a use is not verified
        // it should send verification SMS and redirect him to verification page.
        $user = factory(User::class)->state('not_verified')->create([
            'password' => bcrypt($password),
        ]);

        $data = [
            'phone_number' =>  $user->phone_number,
            'password' => $password,
        ];

        $response = $this->ajax('post', route('auth.signin'), $data);

        $redirectRoute = route('auth.verify', ['verification_id' => Auth::user()->verification_id]);
        $response->assertRedirect($redirectRoute);

        // Session is created
        $this->assertTrue(Auth::check());

        // When a user is verified
        // it should send 2fa code and redirect him to 2fa code page.
        $user->verified_at = \Carbon\Carbon::now();
        $user->save();

        Auth::logout();

        $this
            ->ajax('post', route('auth.signin'), $data)
            ->assertRedirect(route('auth.2fa'))
        ;

        // Session is created
        $this->assertTrue(Auth::check());
    }

    /**
     * @dataProvider valid_user_data
     * @test
     * @param $data
     */
    public function user_cant_login_with_invalid_credentials($data)
    {
        $this
            ->ajax('post', route('auth.signin'), $data)
            ->assertStatus(422)
        ;
    }

    /**
     * @test
     */
    public function user_can_logout()
    {
        $this->signin();

        $this
            ->get(route('auth.logout'))
            ->assertRedirect(route('landing_page'))
        ;

        // Session is destroyed
        $this->assertFalse(Auth::check());
    }

    /**
     * When a user is already logged in, but he's not verified yet he have to
     * be redirected to the verification page. This scenario occurs when a user refresh the verification page,
     * or close then open it.
     *
     * The logic is handled within a Middleware.
     * @test
     */
    public function it_only_redirect_unverified_users_to_verification_page()
    {
        // An unverified user
        // --
        $user = factory(User::class)->state('not_verified')->create();
        $this->signin($user);

        $verificationRoute = route('auth.verify', ['verification_id' => $user->verification_id]);

        $this
            ->get(route('home'))
            ->assertRedirect($verificationRoute)
        ;

        // A verified user
        // --
        $this->signin();
        $this
            ->get(route('home'))
            ->assertOk()
        ;
    }

    /**
     * When a user is already logged in, but he did not pass the 2FA yet. He have to
     * be redirected to the verification page. This scenario occurs when a user refresh the 2FA page,
     * or close then open it.
     *
     * The logic is handled within a Middleware.
     * @test
     */
    public function it_redirect_the_right_users_to_2fa_page()
    {
        // An invalid user session (Did not pass 2FA).
        // --
        $user = factory(User::class)->state('needs_2fa')->create();
        $this->signin($user);

        $this
            ->get(route('home'))
            ->assertRedirect(route('auth.2fa'))
        ;

        // A valid user session.
        // --
        $this->signin();
        $this
            ->get(route('home'))
            ->assertOk()
        ;
    }

    /**
     * @test
     */
    public function it_verify_users()
    {
        $user = factory(User::class)->state('not_verified')->create();
        $this->signin($user);

        $verificationRoute = route('auth.verify', ['verification_id' => $user->verification_id]);

        // It does not allow invalid verification codes
        // --
        $data = [
            'code' => null,
        ];

        $this
            ->json('post', $verificationRoute, $data)
            ->assertJsonValidationErrors([
                'code',
            ])
        ;

        // It properly handle valid verification codes
        // --
        $data = [
            'code' => 123456,
        ];

        $this
            ->json('post', $verificationRoute, $data)
            ->assertRedirect(route('home'))
        ;

        $user->refresh();

        $this->assertNull($user->verification_id);
        $this->assertNotNull($user->verified_at);
    }

    /**
     * @test
     */
    public function it_accept_two_fa_codes()
    {
        $user = factory(User::class)->state('needs_2fa')->create();
        $this->signin($user);

        // It does not allow invalid verification IDs
        // --
        $data = [
            'code' => null,
        ];

        $this
            ->json('post', route('auth.2fa'), $data)
            ->assertJsonValidationErrors([
                'code',
            ])
        ;

        // It properly handle valid verification IDs
        // --
        $data = [
            'code' => 123456,
        ];

        $this
            ->json('post', route('auth.2fa'), $data)
            ->assertRedirect(route('home'))
        ;

        $user->refresh();
        $this->assertNull($user->two_fa_code);
    }

    // Data providers
    // --
    public function invalid_user_data()
    {
        return [
            [
                [
                    'phone_number' => '',
                    'password' => '123456',
                ],
                [
                    'phone_number',
                ]
            ],

            [
                [
                    'phone_number' => 'aa',
                    'password' => '',
                ],
                [
                    'phone_number',
                    'password',
                ]
            ],
        ];
    }

    public function valid_user_data()
    {
        return [
            [
                [
                    'phone_number' => '+21699999999',
                    'password' => '123456',
                ]
            ],
        ];
    }
}
