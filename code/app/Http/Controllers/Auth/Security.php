<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\TwoFactorAuthRequest;
use App\Http\Requests\Auth\VerificationRequest;
use App\Repositories\UsersRepository;
use App\Models\User;
use Auth;

class Security extends \App\Http\Controllers\Controller
{

    /**
     * GET /auth/verify/{verification_id}
     *
     * Return the verification code form page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function verification()
    {
        if (Auth::user()->isVerified()) {
            return redirect()->route('home');
        }

        return view('pages.auth.verify');
    }

    /**
     * POST /auth/verify/{verification_id}
     *
     * Check the integrity of the entered code.
     *
     * @param VerificationRequest $request
     * @param UsersRepository $users
     * @param $verification_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(VerificationRequest $request, UsersRepository $users, $verification_id)
    {
        $code = $request->validated()['code'];

        $user = $users->getByVerificationId($verification_id) ?? Auth::user();

        if (! $user->checkVerificationCode($code)) {
            return $this->validationErrors([
                'code' => 'Invalid verification code.',
            ]);
        }

        $users->markAsVerified($user);

        return $this->redirectAndConsiderAjax(route('home'));
    }

    /**
     * GET /auth/two_factor_auth
     *
     * Return the two factor authentication form page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function twoFaPage()
    {
        if (! Auth::user()->needsTwoFa()) {
            return redirect()->route('home');
        }

        return view('pages.auth.two_fa');
    }

    /**
     * POST /auth/two_factor_auth
     *
     * Check the integrity of the entered 2fa code.
     *
     * @param TwoFactorAuthRequest $request
     * @param UsersRepository $users
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function check2FA(TwoFactorAuthRequest $request, UsersRepository $users)
    {
        $code = $request->validated()['code'];

        /**
         * @var User $user
         */
        $user = Auth::user();

        if (! $user->check2FaCode($code)) {
            return $this->validationErrors([
                'code' => 'Invalid two factor authentication code.'
            ]);
        }

        $users->clear2FaStatus($user);

        return $this->redirectAndConsiderAjax(route('home'));
    }

    /**
     * POST /auth/resend_verification_code
     *
     * Send another verification code for the user if possible.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationCode()
    {
        if (! Auth::user()->canReceiveCode()) {
            return $this->validationErrors([
                'sms_rate' => 'We\'ve already sent an SMS for you.'
            ]);
        }

        $id = Auth::user()->sendVerificationCode();

        if ($id === false) {
            return $this->jsonError(
                'Internal service error. We\'re sorry for this inconvenient.',
                500
            );
        }

        return $this->jsonSuccess('Verification code has been sent.');
    }

    /**
     * POST /auth/auth/resend_2fa_code
     *
     * Send another 2fa code for the user if possible.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendTwoFaCode()
    {
        if (! Auth::user()->canReceiveCode()) {
            return $this->validationErrors([
                'sms_rate' => 'We\'ve already sent an SMS for you.'
            ]);
        }

        $id = Auth::user()->send2FaCode();

        if ($id === false) {
            return $this->jsonError(
                'Internal service error. We\'re sorry for this inconvenient.',
                500
            );
        }

        return $this->jsonSuccess('Two factor authentication code has been sent.');
    }
}
