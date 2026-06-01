<?php

use App\Http\Controllers\InstagramController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    $user = Auth::user();

    // If user is already connected, redirect to dashboard
    if ($user && $user->activeInstagramAccounts()->count() > 0) {
        return redirect('/dashboard');
    }

    return view('welcome');
});

// Instagram OAuth Routes
Route::get('/auth/instagram/redirect', [InstagramController::class, 'redirect'])->name('instagram.redirect');
Route::get('/auth/instagram/callback', [InstagramController::class, 'callback'])->name('instagram.callback');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [InstagramController::class, 'dashboard'])->name('dashboard');
    Route::post('/auth/instagram/disconnect/{account}', [InstagramController::class, 'disconnect'])->name('instagram.disconnect');
});

// Public profile page — MUST be last to avoid catching other routes
Route::get('/{username}', [InstagramController::class, 'profile'])->name('profile.show');
