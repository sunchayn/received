<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ChangingProfileDataRequest;
use App\Http\Requests\Settings\ChangingUsernameRequest;
use Illuminate\Http\Request;
use Auth;

class Profile extends Controller
{
    /**
     * PATCH /settings/profile/username
     *
     * Change current user username.
     *
     * @param ChangingUsernameRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function username(ChangingUsernameRequest $request)
    {
        Auth::user()->update($request->validated());
        return $this->jsonData(Auth::user());
    }

    /**
     * PATCH /settings/profile/
     *
     * Update user profile's data.
     *
     * @param ChangingProfileDataRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(ChangingProfileDataRequest $request)
    {
        Auth::user()->update($request->validated());
        return $this->jsonData(Auth::user());
    }
}
