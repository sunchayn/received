<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\SMS\Exceptions\VerificationCodeNotSentException;
use App\Services\SMS\Exceptions\VerificationNotAchievedException;
use App\Services\SMS\ProviderInterface as SMSProviderInterface;
use Carbon\Carbon;
use Auth;

class Verification extends \App\Http\Controllers\Controller
{
    public function verificationPage()
    {
        return 'Verification page.';
    }

    public function verify($verification_id)
    {
        $data = request()->validate([
            'code' => 'required',
        ]);

        $user = User::where('verification_id', $verification_id)->first();

        if (! $this->verifyRequestId($verification_id, $data['code'])) {
            return response()->json([
                'errors' => [
                    'code' => 'Invalid verification code.',
                ]
            ], 422);
        }

        $user->verification_id = null;
        $user->verified_at = Carbon::now();
        $user->save();

        return redirect()->route('home');
    }

    public function resendVerificationCode()
    {
        $verification_id = $this->generateVerificationId(Auth::user()->phone_number);

        if ($verification_id === false) {
            return response()->json([
                'error' => 'Internal service error. We\'re sorry for this inconvenient.',
            ], 500);
        }

        Auth::user()->verification_id = $verification_id;
        Auth::user()->save();

        return response()->json([
            'success' => 'Verification code has been sent.',
        ], 200);
    }

    public function twoFAPage()
    {
        return 'Two factor authentication page.';
    }

    public function check2FA()
    {
        $data = request()->validate([
            'code' => 'required',
        ]);

        if (! $this->verifyTwoFACode(Auth::user(), $data['code'])) {
            return response()->json([
                'error' => 'Invalid two factor authentication code.',
            ], 422);
        }

        Auth::user()->ongoing_two_fa = false;
        Auth::user()->save();

        return redirect()->route('home');
    }

    /**
     * todo: Add request rate limit 1 code per minute
     */
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

    protected function verifyRequestId(string $verificationId, string $code)
    {
        /**
         * @var SMSProviderInterface $smsProvider
         */
        $smsProvider = app()->make('SMS');

        try {
            return $smsProvider->verify($verificationId, $code);
        } catch (VerificationNotAchievedException $e) {
            return false;
        }
    }

    protected function verifyTwoFACode(\Illuminate\Contracts\Auth\Authenticatable $user, string $code)
    {
        /**
         * @var User $user
         */

        return true;
    }
}
