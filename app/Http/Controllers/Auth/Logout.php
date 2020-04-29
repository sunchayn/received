<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\RedirectResponse;

class Logout extends Controller
{
    /**
     * GET /auth/logout.
     *
     * Logout the current user
     * @return RedirectResponse
     */
    public function logout()
    {
        Auth::logout();

        return redirect()->route('landing_page');
    }
}
