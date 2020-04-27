<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ChangingPasswordRequest;
use App\Http\Requests\Settings\ChangingPhoneRequest;
use App\Http\Requests\Settings\VerifyingPhoneRequest;
use App\Repositories\UsersRepository;
use Auth;
use App\Models\User;

class Account extends Controller
{
    /**
     * POST /settings/account/change_phone
     *
     * Start a phone changing process.
     *
     * @param ChangingPhoneRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePhone(ChangingPhoneRequest $request)
    {
        $data = $request->validated();

        /**
         * @var User $user
         */
        $user = Auth::user();

        // Delete previous ongoing verification
        if ($user->ongoingNewPhoneVerification) {
            $user->ongoingNewPhoneVerification->delete();
        }

        // Send verification code
        $phoneNumber = $phoneNumber = '+' . $data['country_code'] . $data['phone_number'];
        $user->sendVerificationCode($phoneNumber);

        $user->ongoingNewPhoneVerification()->create($data);

        return $this->jsonData([
            'verification_route' => route('settings.verify_phone', ['verification_id' => $user->verification_id])
        ]);
    }

    /**
     * POST /settings/account/verify_phone/{verification_id}
     *
     * Verify a phone number and update user data if it's valid.
     *
     * @param VerifyingPhoneRequest $request
     * @param UsersRepository $users
     * @param $verification_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyNewPhone(VerifyingPhoneRequest $request, UsersRepository $users, $verification_id)
    {
        $code = $request->validated()['code'];

        $user = $users->getByVerificationId($verification_id);

        if (! $user->ongoingNewPhoneVerification) {
            return $this->forbidden();
        }

        if (! $user->checkVerificationCode($code)) {
            return $this->validationErrors([
                'code' => ['Invalid verification code.'],
            ]);
        }

        $user = $users->confirmOngoingPhoneVerification($user);
        return $this->jsonData($user);
    }

    /**
     * PATCH /settings/account/password
     *
     * Change current user password.
     *
     * @param ChangingPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function password(ChangingPasswordRequest $request)
    {
        $data = $request->validated();

        Auth::user()->update([
            'password' => bcrypt($data['password']),
        ]);

        return $this->jsonSuccess('The password has been successfully changed!');
    }
}
