<?php

namespace App\Http\Controllers\Auth;

use App\Services\SMS\Exceptions\UserNotCreatedException;
use App\Services\SMS\Exceptions\VerificationCodeNotSentException;
use App\Services\SMS\Exceptions\TwoFactorCodeNotSentException;
use App\Services\SMS\ProviderInterface as SMSProviderInterface;
use Auth;
use Validator;
use App\Models\User;
use Illuminate\Support\Str;

class Authentication extends \App\Http\Controllers\Controller
{
    public function signup()
    {
        return 'Signup page.';
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'phone_number' => 'required|regex:/^[0-9]{6,}$/|unique:users',
            'country_code' => 'required|regex:/^\+?[0-9]{3,4}$/',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            if (request()->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            } else {
                return redirect()
                    ->route('auth.signup')
                    ->withErrors($validator)
                    ->withInput()
                ;
            }
        }

        $data = $validator->validated();

        $user = User::create([
            'password' => bcrypt($data['password']),
            'username' => Str::uuid(),
            'phone_number' => $data['phone_number'],
            'country_code' => str_replace('+', '', $data['country_code']),
        ]);

        $verification_id = $this->generateVerificationId($data['phone_number']);

        if ($verification_id === false) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'Internal service error. We\'re sorry for this inconvenient.',
                ], 500);
            } else {
                return redirect()
                    ->route('auth.signup')
                    ->withErrors([
                        'error' => 'Internal service error. We\'re sorry for this inconvenient.'
                    ])
                    ->withInput()
                ;
            }
        }

        $user->verification_id = $verification_id;
        $user->save();

        Auth::login($user);

        return redirect()->route('auth.verify', ['verification_id' => $verification_id]);
    }

    public function signin()
    {
        return 'Signin page.';
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('landing_page');
    }

    public function attempt()
    {
        $credentials = request()->only(['phone_number', 'password']);

        if (Auth::attempt($credentials, true)) {
            if (Auth::user()->isVerified()) {
                Auth::user()->ongoing_two_fa = true;
                Auth::user()->save();
                $this->sendTwoFactorAuthCode(Auth::user());
                return redirect()->route('auth.2fa');
            } else {
                $verification_id = $this->generateVerificationId($credentials['phone_number']);
                Auth::user()->verification_id = $verification_id;
                Auth::user()->save();

                return redirect()->route('auth.verify', ['verification_id' => $verification_id]);
            }
        }

        return response()->json([
            'error' => 'The given credentials does not match!',
        ], 422);
    }

    protected function generateVerificationId(String $phoneNumber)
    {
        /**
         * @var SMSProviderInterface $smsProvider
         */
        $smsProvider = app()->make('SMS');

        try {
            return $smsProvider->sendVerificationCode($phoneNumber);
        } catch (VerificationCodeNotSentException $e) {
            return false;
        }
    }

    protected function sendTwoFactorAuthCode($user): bool
    {
        /**
         * @var SMSProviderInterface $smsProvider
         */
        $smsProvider = app()->make('SMS');

        try {
            $smsProvider->sendTwoFactorCode($user);
            return true;
        } catch (UserNotCreatedException $e) {
            return false;
        } catch (TwoFactorCodeNotSentException $e) {
            return false;
        }
    }
}
