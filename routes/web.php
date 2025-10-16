<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Examples\SearchSelectExample;
use App\Livewire\Examples\ToastrExample;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Forgot Password
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    // Reset Password
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email')
        ]);
    })->name('password.reset');
});

// Email Verification (can be accessed by both guest and authenticated users)
Route::get('/verify-email/{token?}', function ($token = null) {
    return view('auth.verify-email', ['token' => $token]);
})->name('verification.verify');

// Phone Verification (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/verify-phone', function () {
        return view('auth.verify-phone');
    })->name('verification.phone');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// Component Examples (for development/testing)
Route::get('/examples/search-select', SearchSelectExample::class)->name('examples.search-select');
Route::get('/examples/toastr', ToastrExample::class)->name('examples.toastr');
