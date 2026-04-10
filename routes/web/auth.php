<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Auth\PageController as AuthPageController;

// Routes for login and register
Route::middleware('guest')->group(function () {
    // Route::get('/auth', AuthPageController::class)->middleware('guest')->name('auth.login');

    /**
     * Routes for login
     * GET: index [auth/login]
     * POST: store [auth/login]
     */
    Route::resource('/auth/login', LoginController::class)
    ->middleware('guest')
    ->names([
        'index' => 'auth.login',
        'store' => 'auth.login'
    ])->only(['index', 'store']);

    /**
     * Routes for registration
     * GET: index [auth/login]
     * POST: store [auth/login]
     */
    Route::resource('/auth/register', RegisterController::class)
    ->middleware('guest')
    ->names([
        'index' => 'auth.register',
        'store' => 'auth.register'
    ])->only(['index', 'store']);

    Route::get('/auth/google', [OAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [OAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/auth/github', [OAuthController::class, 'redirectToGithub'])->name('auth.github');
    Route::get('/auth/github/callback', [OAuthController::class, 'handleGithubCallback'])->name('auth.github.callback');
});

// Routes for logout
Route::post('/auth/logout', LogoutController::class)
    ->middleware('auth')
    ->name('auth.logout');
