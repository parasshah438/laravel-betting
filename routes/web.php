<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BettingController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/live-betting', [BettingController::class, 'live'])->name('betting.live');

// Demo Routes
Route::get('/modal-demo', function () {
    return view('demo.modals');
})->name('modal.demo');

Route::get('/responsive-test', function () {
    return view('demo.responsive');
})->name('responsive.test');

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    // Account Management
    Route::get('/account', function () {
        return view('betting.account');
    })->name('account');
    
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/settings', [AccountController::class, 'updateSettings'])->name('account.settings.update');
    Route::put('/account/security', [AccountController::class, 'updateSecurity'])->name('account.security.update');
    
    // Wallet & Transactions
    Route::post('/account/deposit', [AccountController::class, 'deposit'])->name('account.deposit');
    Route::post('/account/withdraw', [AccountController::class, 'withdraw'])->name('account.withdraw');
    Route::get('/account/transactions', [AccountController::class, 'transactions'])->name('account.transactions');
    
    // Betting History
    Route::get('/betting-history', function () {
        return view('betting.history');
    })->name('betting.history');
    
    Route::get('/betting-history/{bet}', [BettingController::class, 'betDetails'])->name('betting.details');
    
    // Live Betting (authenticated users get enhanced features)
    Route::post('/place-bet', [BettingController::class, 'placeBet'])->name('betting.place');
    Route::post('/cash-out/{bet}', [BettingController::class, 'cashOut'])->name('betting.cashout');
    
    // Sports & Odds
    Route::get('/sports/{sport}', [BettingController::class, 'sport'])->name('betting.sport');
    Route::get('/match/{match}', [BettingController::class, 'match'])->name('betting.match');
});

// API Routes for AJAX requests (public for guest users, enhanced for authenticated)
Route::prefix('api/web')->group(function () {
    // Real-time odds updates (public)
    Route::get('/odds/{match}', [BettingController::class, 'getOdds'])->name('api.odds');
    Route::get('/live-matches', [BettingController::class, 'getLiveMatches'])->name('api.live.matches');
    
    // Authenticated API routes
    Route::middleware('auth:sanctum')->group(function () {
        // Bet slip management
        Route::post('/bet-slip/add', [BettingController::class, 'addToBetSlip'])->name('api.betslip.add');
        Route::delete('/bet-slip/remove/{selection}', [BettingController::class, 'removeFromBetSlip'])->name('api.betslip.remove');
        Route::get('/bet-slip', [BettingController::class, 'getBetSlip'])->name('api.betslip.get');
        
        // Account quick actions
        Route::get('/balance', [AccountController::class, 'getBalance'])->name('api.balance');
        Route::get('/notifications', [AccountController::class, 'getNotifications'])->name('api.notifications');
    });
});
