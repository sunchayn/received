<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.landing');
})->name('landing_page')->middleware(['guest', 'clean_session']);

// Authentication
// --
Route::prefix('/auth')->name('auth.')->middleware('guest')->group(function () {
    // Signup
    // --
    Route::get('/signup', [
        'uses' =>  'Auth\Signup@signup',
        'as' => 'signup'
    ]);

    Route::post('/signup', [
        'uses' =>  'Auth\Signup@store',
    ]);

    // Signin
    // --
    Route::get('/signin', [
        'uses' =>  'Auth\Signin@signin',
        'as' => 'signin'
    ]);

    Route::post('/signin', [
        'uses' =>  'Auth\Signin@attempt',
    ]);
});

Route::middleware('auth')->group(function () {
    // Security
    // --
    Route::prefix('auth')->name('auth.')->group(function () {
        // Logout
        // --
        Route::get('/logout', [
            'uses' =>  'Auth\Logout@logout',
            'as' => 'logout'
        ]);

        // Verify
        // --
        Route::get('/verify/{verification_id}', [
            'uses' =>  'Auth\Security@verification',
            'as' => 'verify'
        ]);

        Route::post('/verify/{verification_id}', [
            'uses' =>  'Auth\Security@verify',
        ]);

        // 2FA
        // --
        Route::get('/two_factor_auth', [
            'uses' =>  'Auth\Security@twoFaPage',
            'as' => '2fa'
        ]);

        Route::post('/two_factor_auth', [
            'uses' =>  'Auth\Security@check2FA',
        ]);

        // Resend SMS codes
        // --
        Route::post('/resend_verification_code', [
            'uses' =>  'Auth\Security@resendVerificationCode',
            'as' => 'resend_verification_code'
        ]);

        Route::post('/resend_2fa_code', [
            'uses' =>  'Auth\Security@resendTwoFaCode',
            'as' => 'resend_2fa_code'
        ]);
    });
});

Route::middleware(['auth', 'clean_session'])->group(function () {
    // App main entry point
    Route::get('/app', function () {
        return view('welcome');
    })->name('home');

    // Folders
    Route::prefix('folders')->name('folders.')->group(function () {

        Route::get('/all', [
            'uses' =>  'Folders@all',
            'as' => 'all',
        ]);

        Route::post('/create', [
            'uses' =>  'Folders@store',
            'as' => 'create',
        ]);

        Route::patch('/share/{folder}', [
            'uses' =>  'Folders@share',
            'as' => 'share',
        ]);

        Route::patch('/revoke/{folder}', [
            'uses' =>  'Folders@revoke',
            'as' => 'revoke',
        ]);

        Route::patch('/edit/{folder}', [
            'uses' =>  'Folders@save',
            'as' => 'edit',
        ]);

        Route::delete('/delete/{folder}', [
            'uses' =>  'Folders@delete',
            'as' => 'delete',
        ]);
    });
});
