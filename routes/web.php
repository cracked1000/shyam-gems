<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\TwoFactorController;
use App\Livewire\Welcome;
use App\Livewire\ClientDashboard;
use App\Livewire\SellerDashboard;
use App\Livewire\ProfileEdit;
use App\Livewire\AccountSettings;
use App\Livewire\Feed;
use App\Livewire\Messages;
use App\Livewire\Profile;
use App\Livewire\ForgotPassword;
use App\Livewire\ResetPassword;
use Illuminate\Support\Facades\Route;

// Home Route
Route::get('/', Welcome::class)->name('home');

// Authentication Routes (Guest Only)
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', function () {
        return view('auth.login')->layout('layouts.traditional');
    })->name('login');
    
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    // Forgot Password Routes
    Route::get('/forgot-password', ForgotPassword::class)
        ->name('forgot-password.show');

    // Reset Password Route
    Route::get('/reset-password/{token}', ResetPassword::class)
        ->name('password.reset');

    // Seller Registration Routes
    Route::get('/register/seller', function () {
        return view('auth.register', ['role' => 'seller'])->layout('layouts.traditional');
    })->name('seller.register.show');
    
    Route::post('/register/seller', [RegisteredUserController::class, 'store'])
        ->name('seller.register');

    // Client Registration Routes
    Route::get('/register/client', function () {
        return view('auth.register', ['role' => 'client'])->layout('layouts.traditional');
    })->name('client.register.show');
    
    Route::post('/register/client', [RegisteredUserController::class, 'store'])
        ->name('client.register');
});

// Email Verification Routes (Auth Required)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Two-Factor Authentication Routes (Auth + Verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/2fa/setup', [TwoFactorController::class, 'setup'])
        ->name('2fa.setup');
    
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])
        ->name('2fa.enable');
    
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])
        ->name('2fa.disable');
    
    Route::get('/2fa/challenge', [TwoFactorController::class, 'challenge'])
        ->middleware('signed')
        ->name('2fa.challenge');
    
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])
        ->name('2fa.verify');
});

// Protected Routes (Auth + Verified + 2FA)
Route::middleware(['auth', 'verified', '2fa.verified'])->group(function () {
    
    // Dashboard Route with proper redirection
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get dashboard route based on user method
        $dashboardRoute = $user->getDashboardRouteName();
        
        if ($dashboardRoute && Route::has($dashboardRoute)) {
            return redirect()->route($dashboardRoute);
        }
        
        // Fallback to home if no valid dashboard route found
        return redirect()->route('home');
    })->name('dashboard');
    
    // Role-specific Dashboard Routes
    Route::get('/client/dashboard', ClientDashboard::class)
        ->middleware('role:client')
        ->name('client.dashboard');
    
    Route::get('/seller/dashboard', SellerDashboard::class)
        ->middleware('role:seller')
        ->name('seller.dashboard');
    
    Route::get('/my-client-dashboard', ClientDashboard::class)
        ->middleware('role:client')
        ->name('my.client.dashboard');
    
    // Profile Routes
    Route::get('/profile/edit', ProfileEdit::class)
        ->name('profile.edit-settings');
    
    Route::get('/profile/{username}', Profile::class)
        ->where('username', '[a-zA-Z0-9_-]+')
        ->name('profile.show');
    
    // Core App Routes
    Route::get('/feed', Feed::class)
        ->name('feed');
    
    Route::get('/messages', Messages::class)
        ->name('messages.index');
    
    Route::get('/account-settings', AccountSettings::class)
        ->name('account.edit');
    
    // Role-specific Route Groups
    Route::middleware('role:seller')->group(function () {
        // Seller-specific routes can be added here
    });
    
    Route::middleware('role:client')->group(function () {
        // Client-specific routes can be added here
    });
});

// Public Information Route
Route::get('/learn-more', function () {
    return view('learn-more')->layout('components.layouts.app');
})->name('learn-more');

// Logout Route (Auth Required)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');