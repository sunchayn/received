<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('landing_page');

// Authentication
// --
Route::prefix('/auth')->name('auth.')->middleware('guest')->group(function () {
    // Signup
    Route::get('/signup', 'Auth\Authentication@signup')->name('signup');
    Route::post('/signup', 'Auth\Authentication@store');

    // Signin
    Route::get('/signin', 'Auth\Authentication@signin')->name('signin');
    Route::post('/signin', 'Auth\Authentication@attempt');
});

Route::middleware('auth')->group(function () {

    // Authentication
    // --
    Route::prefix('auth')->name('auth.')->group(function () {
        // Logout
        Route::get('/logout', 'Auth\Authentication@logout')->name('logout');

        // Verify
        Route::get('/verify/{verification_id}', 'Auth\Verification@verificationPage')->name('verify');
        Route::post('/verify/{verification_id}', 'Auth\Verification@verify');

        Route::post('/resend_verification_code', 'Auth\Verification@resendVerificationCode')->name('resend_verification_code');

        // 2FA
        Route::get('/two_factor_auth', 'Auth\Verification@twoFAPage')->name('2fa');
        Route::post('/two_factor_auth', 'Auth\Verification@check2FA');
    });

    // App main entry point
    Route::get('/app', function () {
        return view('welcome');
    })->name('home');
});
