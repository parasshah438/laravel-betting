<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import all API controllers
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\Wallet\WalletController;
use App\Http\Controllers\Api\Sports\SportsController;
use App\Http\Controllers\Api\Match\MatchController;
use App\Http\Controllers\Api\Betting\BettingController;
use App\Http\Controllers\Api\Promotion\PromotionController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\MatchManagementController;
use App\Http\Controllers\Api\Notification\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1
Route::group(['prefix' => 'v1'], function () {
    
    /*
    |--------------------------------------------------------------------------
    | Authentication Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'auth'], function () {
        // Public authentication routes
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
        
        // Protected authentication routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | User Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['prefix' => 'profile'], function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
            Route::put('/preferences', [ProfileController::class, 'updatePreferences']);
            
            // KYC Document routes
            Route::get('/kyc-documents', [ProfileController::class, 'getKycDocuments']);
            Route::post('/kyc-documents', [ProfileController::class, 'uploadKycDocument']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Wallet & Financial Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['prefix' => 'wallet'], function () {
            Route::get('/', [WalletController::class, 'getWallets']);
            Route::get('/{currency}', [WalletController::class, 'getWallet']);
            Route::get('/{currency}/balance', [WalletController::class, 'getBalance']);
            
            // Deposit routes
            Route::post('/deposit', [WalletController::class, 'deposit']);
            Route::get('/deposit/methods', [WalletController::class, 'getDepositMethods']);
            Route::get('/deposit/history', [WalletController::class, 'getDepositHistory']);
            
            // Withdrawal routes
            Route::post('/withdraw', [WalletController::class, 'withdraw']);
            Route::get('/withdraw/methods', [WalletController::class, 'getWithdrawalMethods']);
            Route::get('/withdraw/history', [WalletController::class, 'getWithdrawalHistory']);
            
            // Transaction routes
            Route::get('/transactions', [WalletController::class, 'getTransactions']);
            Route::get('/transactions/{transactionId}', [WalletController::class, 'getTransaction']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Sports & Match Data Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'sports'], function () {
        // Public sports data routes
        Route::get('/', [SportsController::class, 'getSports']);
        Route::get('/{sportId}', [SportsController::class, 'getSport']);
        Route::get('/{sportId}/leagues', [SportsController::class, 'getLeagues']);
        Route::get('/leagues/{leagueId}', [SportsController::class, 'getLeague']);
        Route::get('/leagues/{leagueId}/teams', [SportsController::class, 'getTeams']);
        Route::get('/teams/{teamId}', [SportsController::class, 'getTeam']);
        Route::get('/teams/{teamId}/matches', [SportsController::class, 'getTeamMatches']);
        
        // Search functionality
        Route::get('/search/leagues', [SportsController::class, 'searchLeagues']);
        Route::get('/search/teams', [SportsController::class, 'searchTeams']);
    });

    /*
    |--------------------------------------------------------------------------
    | Match Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'matches'], function () {
        // Public match routes
        Route::get('/', [MatchController::class, 'getMatches']);
        Route::get('/live', [MatchController::class, 'getLiveMatches']);
        Route::get('/upcoming', [MatchController::class, 'getUpcomingMatches']);
        Route::get('/finished', [MatchController::class, 'getFinishedMatches']);
        Route::get('/featured', [MatchController::class, 'getFeaturedMatches']);
        Route::get('/{matchId}', [MatchController::class, 'getMatch']);
        Route::get('/{matchId}/betting-options', [MatchController::class, 'getBettingOptions']);
        Route::get('/{matchId}/statistics', [MatchController::class, 'getMatchStatistics']);
        
        // Protected match routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/{matchId}/user-bets', [MatchController::class, 'getUserMatchBets']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Betting Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['prefix' => 'bets'], function () {
            // Place and manage bets
            Route::post('/', [BettingController::class, 'placeBet']);
            Route::get('/', [BettingController::class, 'getUserBets']);
            Route::get('/{betId}', [BettingController::class, 'getBet']);
            Route::post('/{betId}/cashout', [BettingController::class, 'cashOut']);
            
            // Betting statistics
            Route::get('/statistics/overview', [BettingController::class, 'getStatistics']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Promotion Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['prefix' => 'promotions'], function () {
            // Available promotions
            Route::get('/', [PromotionController::class, 'getPromotions']);
            Route::get('/{promotionId}', [PromotionController::class, 'getPromotion']);
            Route::post('/{promotionId}/claim', [PromotionController::class, 'claimPromotion']);
            
            // Promo codes
            Route::post('/promo-code', [PromotionController::class, 'applyPromoCode']);
            
            // User bonuses
            Route::get('/bonuses', [PromotionController::class, 'getUserBonuses']);
            Route::get('/bonuses/{bonusId}/progress', [PromotionController::class, 'getBonusProgress']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Notification Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [NotificationController::class, 'getNotifications']);
            Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
            Route::put('/{notificationId}/read', [NotificationController::class, 'markAsRead']);
            Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{notificationId}', [NotificationController::class, 'deleteNotification']);
            
            // Notification preferences
            Route::get('/preferences', [NotificationController::class, 'getPreferences']);
            Route::put('/preferences', [NotificationController::class, 'updatePreferences']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->group(function () {
        Route::group(['prefix' => 'admin'], function () {
            
            // Dashboard & Statistics
            Route::get('/dashboard', [AdminController::class, 'getDashboardStats']);
            
            // User Management
            Route::get('/users', [AdminController::class, 'getUsers']);
            Route::get('/users/{userId}', [AdminController::class, 'getUser']);
            Route::put('/users/{userId}/status', [AdminController::class, 'updateUserStatus']);
            
            // Bet Management
            Route::get('/bets', [AdminController::class, 'getBets']);
            
            // Transaction Management
            Route::get('/transactions', [AdminController::class, 'getTransactions']);
            
            // System Settings
            Route::get('/settings', [AdminController::class, 'getSettings']);
            Route::put('/settings', [AdminController::class, 'updateSettings']);
            
            // Audit Logs
            Route::get('/audit-logs', [AdminController::class, 'getAuditLogs']);
            
            // Bulk Notifications
            Route::post('/notifications/bulk', [NotificationController::class, 'sendBulkNotification']);
            
            /*
            |--------------------------------------------------------------------------
            | Match Management Routes (Admin)
            |--------------------------------------------------------------------------
            */
            Route::group(['prefix' => 'matches'], function () {
                Route::get('/', [MatchManagementController::class, 'getMatches']);
                Route::post('/', [MatchManagementController::class, 'createMatch']);
                Route::put('/{matchId}', [MatchManagementController::class, 'updateMatch']);
                Route::get('/{matchId}/betting-options', [MatchManagementController::class, 'getMatchBettingOptions']);
                Route::post('/{matchId}/betting-markets', [MatchManagementController::class, 'addBettingMarket']);
                Route::put('/betting-options/{optionId}/odds', [MatchManagementController::class, 'updateOdds']);
                Route::put('/{matchId}/toggle-betting', [MatchManagementController::class, 'toggleBetting']);
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Cart Routes (Compatibility)
    |--------------------------------------------------------------------------
    */
    Route::get('/cart/count', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'count' => 0,
                'message' => 'Cart functionality not available in betting platform'
            ]
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Public Information Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'public'], function () {
        // System information
        Route::get('/system-info', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'app_name' => config('app.name'),
                    'version' => '1.0.0',
                    'api_version' => 'v1',
                    'timezone' => config('app.timezone'),
                    'supported_currencies' => ['USD', 'EUR', 'GBP', 'BTC', 'ETH'],
                    'supported_languages' => ['en', 'es', 'fr', 'de'],
                ]
            ]);
        });
        
        // Health check
        Route::get('/health', function () {
            return response()->json([
                'success' => true,
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'services' => [
                    'database' => 'connected',
                    'cache' => 'connected',
                    'queue' => 'running',
                ]
            ]);
        });
        
        // API Documentation link
        Route::get('/docs', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'documentation_url' => url('/api/documentation'),
                    'postman_collection' => url('/api/postman-collection'),
                    'swagger_ui' => url('/api/swagger'),
                ]
            ]);
        });
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Routes
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'error' => 'The requested API endpoint does not exist'
    ], 404);
});
