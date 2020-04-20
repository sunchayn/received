<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Str;

class UserSessionVerification
{
    /**
     * It redirect the authenticated users to the proper route if they need
     * verification or two factor authentication
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        if (Auth::user()->isVerified()) {
            if (Auth::user()->needsTwoFA() && !Str::contains($request->path(), 'two_factor_auth')) {
                return redirect()->route('auth.2fa');
            }

            return $next($request);
        }

        if (Str::contains($request->path(), 'verify')) {
            return $next($request);
        }

        return redirect()->route('auth.verify', ['verification_id' => Auth::user()->verification_id]);
    }
}
