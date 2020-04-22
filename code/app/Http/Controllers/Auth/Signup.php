<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\UsersRepository;
use Auth;
use App\Http\Requests\Auth\SignupRequest;
use Illuminate\Http\RedirectResponse;

class Signup extends \App\Http\Controllers\Controller
{
    /**
     * GET /auth/signup
     *
     * Return signup form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signup()
    {
        return view('pages.auth.signup');
    }

    /**
     * POST /auth/signup
     *
     * Attempt to create a user, send a verification code with the given data.
     *
     * @param SignupRequest $request
     * @param UsersRepository $users
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(SignupRequest $request, UsersRepository $users)
    {
        $request->validate();

        if ($request->fails()) {
            return $request->sendBackErrors();
        }

        $user = $users->create($request->validated());

        $id = $user->sendVerificationCode();

        if ($id === false) {
            return $this->redirectWithError(
                route('auth.signup'),
                'Internal service error. We\'re sorry for this inconvenient.',
                500
            );
        }

        Auth::login($user);

        return redirect()->route('auth.verify', ['verification_id' => $id]);
    }
}
