<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Models\User;

class Signin extends \App\Http\Controllers\Controller
{
    /**
     * GET /auth/signin
     *
     * Return signin form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signin()
    {
        return view('pages.auth.signin');
    }

    /**
     * POST /auth/signin
     *
     * Attempt to login the user with the given credentials
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function attempt()
    {
        $credentials = request()->only(['phone_number', 'password']);

        if (Auth::attempt($credentials, true)) {
            /**
             * @var User $user
             */
            $user = Auth::user();

            if ($user->isVerified()) {
                $user->send2FaCode();
                return $this->redirectAndConsiderAjax(route('auth.2fa'));
            }

            $id = $user->sendVerificationCode();
            return $this->redirectAndConsiderAjax(route('auth.verify', ['verification_id' => $id]));
        }

        return $this->jsonUnprocessableEntity('The given credentials does not match!');
    }
}
