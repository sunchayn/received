<?php

namespace App\Http\Controllers\Auth;

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
            'phone_number' => 'required|regex:/\+?[0-9]{9,}/|unique:users',
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
        $data['password'] = bcrypt($data['password']);
        $data['username'] = Str::uuid();

        $user = User::create($data);

        $verification_id = $this->generateVerificationId($data['phone_number']);
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
                Auth::user()->two_fa_code = $this->generateTwoFACode(Auth::user());

                return redirect()->route('auth.2fa');
            } else {
                $verification_id = $this->generateVerificationId($credentials['phone_number']);
                Auth::user()->verification_id = $verification_id;
                Auth::user()->save();

                return redirect()->route('auth.verify', ['verification_id' => $verification_id]);
            }
        }

        return response()->json([
            'error' => 'The given credentials do not match!',
        ], 422);
    }

    protected function generateVerificationId(String $phoneNumber): String
    {
        return Str::random(10);
    }

    protected function generateTwoFACode(\Illuminate\Foundation\Auth\User $user): bool
    {
        /**
         * @var User $user
         */
        $user->two_fa_code = Str::random(10);
        $user->save();

        return true;
    }
}
