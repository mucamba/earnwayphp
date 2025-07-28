<?php

use App\Http\Controllers\Backend\Auth\AuthController as AdminAuthController;
use App\Http\Controllers\Backend\Auth\ForgetPasswordController as AdminForgetPasswordController;
use App\Http\Controllers\Backend\Auth\LockScreenController;
use App\Http\Controllers\Backend\Auth\TwoFactorVerificationController as AdminTwoFactorVerificationController;
use App\Http\Controllers\Frontend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Frontend\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Frontend\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Frontend\Auth\NewPasswordController;
use App\Http\Controllers\Frontend\Auth\PasswordController;
use App\Http\Controllers\Frontend\Auth\PasswordResetLinkController;
use App\Http\Controllers\Frontend\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\Auth\TwoFactorVerificationController as UserTwoFactorVerificationController;
use App\Http\Controllers\Frontend\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Registration routes
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Authentication routes
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Password reset routes
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {

    // Email verification
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

    // Two-Factor Authentication
    Route::get('two-factor-challenge', [UserTwoFactorVerificationController::class, 'twoFactorChallenge'])->name('user.two-factor-challenge');
    Route::post('two-factor-challenge', [UserTwoFactorVerificationController::class, 'twoFactorAuthenticate'])->name('user.two-factor-authenticate');

    // Password update
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['XSS'], 'as' => 'admin.', 'prefix' => setting('admin_prefix')], function () {
    Route::get('login', [AdminAuthController::class, 'loginView'])->name('login-view');
    Route::post('login', [AdminAuthController::class, 'authenticate'])->name('login');

    // Forget Password
    Route::get('forget-password', [AdminForgetPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.now');
    Route::post('forget-password', [AdminForgetPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.submit');
    Route::get('reset-password/{token}', [AdminForgetPasswordController::class, 'showResetPasswordForm'])->name('reset.password.now');
    Route::post('reset-password', [AdminForgetPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.submit');

    Route::group(['middleware' => ['auth:admin']], function () {

        // Two-Factor Authentication
        Route::get('two-factor-challenge', [AdminTwoFactorVerificationController::class, 'twoFactorChallenge'])->name('two-factor-challenge');
        Route::post('two-factor-challenge', [AdminTwoFactorVerificationController::class, 'twoFactorAuthenticate']);

        // Lock Screen
        Route::get('/lock', [LockScreenController::class, 'lock'])->name('lock');
        Route::get('/lock-screen', [LockScreenController::class, 'show'])->name('lock-screen.show');
        Route::post('/lock-screen', [LockScreenController::class, 'unlock'])->name('lock-screen.unlock');

        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout')->middleware('auth:admin');
    });

});
