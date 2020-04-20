<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
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
                'error' => 'Invalid verification code.',
            ], 422);
        }

        $user->verification_id = null;
        $user->verified_at = Carbon::now();
        $user->save();

        return redirect()->route('home');
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

        Auth::user()->two_fa_code = null;
        Auth::user()->save();

        return redirect()->route('home');
    }

    protected function verifyRequestId(string $verificationId, string $code)
    {
        return true;
    }

    protected function verifyTwoFACode(\Illuminate\Contracts\Auth\Authenticatable $user, string $code)
    {
        /**
         * @var User $user
         */

        return true;
    }
}
