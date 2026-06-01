<?php

use App\Http\Controllers\InstagramController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = Auth::user();

    // If user is already connected, redirect to their profile
    if ($user && $user->instagram_username) {
        return redirect('/' . $user->instagram_username);
    }

    return view('welcome');
});

// Instagram OAuth Routes
Route::get('/auth/instagram/redirect', [InstagramController::class, 'redirect'])->name('instagram.redirect');
Route::get('/auth/instagram/callback', [InstagramController::class, 'callback'])->name('instagram.callback');
Route::post('/auth/instagram/disconnect', [InstagramController::class, 'disconnect'])->name('instagram.disconnect');

// Public profile page — MUST be last to avoid catching other routes
Route::get('/{username}', [InstagramController::class, 'profile'])->name('profile.show');
