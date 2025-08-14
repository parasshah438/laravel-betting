<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bet;
use App\Models\Transaction;
use App\Models\Match;
use App\Models\Promotion;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin,super_admin']);
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'period' => 'nullable|string|in:today,week,month,year',
            'currency' => 'nullable|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $period = $request->get('period', 'month');
        $currency = $request->get('currency', 'USD');

        // Set date range based on period
        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
        }

        // User statistics
        $userStats = [
            'total_users' => User::count(),
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_users' => User::whereHas('bets', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })->count(),
            'verified_users' => User::where('is_verified', true)->count(),
        ];

        // Betting statistics
        $betStats = [
            'total_bets' => Bet::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_stake' => Bet::whereBetween('created_at', [$startDate, $endDate])
                ->where('currency', $currency)
                ->sum('stake'),
            'total_winnings' => Bet::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'won')
                ->where('currency', $currency)
                ->sum('payout'),
            'pending_bets' => Bet::where('status', 'pending')->count(),
            'live_bets' => Bet::where('status', 'pending')
                ->where('is_live_bet', true)
                ->count(),
        ];

        // Financial statistics
        $financialStats = [
            'total_deposits' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'deposit')
                ->where('status', 'completed')
                ->where('currency', $currency)
                ->sum('amount'),
            'total_withdrawals' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'withdrawal')
                ->where('status', 'completed')
                ->where('currency', $currency)
                ->sum('amount'),
            'pending_withdrawals' => Transaction::where('type', 'withdrawal')
                ->where('status', 'pending')
                ->where('currency', $currency)
                ->sum('amount'),
            'net_profit' => 0, // Will be calculated
        ];

        // Calculate net profit (total stakes - total winnings)
        $financialStats['net_profit'] = $betStats['total_stake'] - $betStats['total_winnings'];

        // Match statistics
        $matchStats = [
            'total_matches' => Match::count(),
            'live_matches' => Match::live()->count(),
            'upcoming_matches' => Match::upcoming()->count(),
            'finished_matches' => Match::finished()->count(),
        ];

        // Recent activity
        $recentActivity = [
            'recent_bets' => Bet::with(['user', 'selections.match'])
                ->latest()
                ->limit(10)
                ->get(),
            'recent_transactions' => Transaction::with('user')
                ->latest()
                ->limit(10)
                ->get(),
            'recent_users' => User::latest()
                ->limit(10)
                ->get(['id', 'name', 'email', 'created_at']),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user_stats' => $userStats,
                'bet_stats' => $betStats,
                'financial_stats' => $financialStats,
                'match_stats' => $matchStats,
                'recent_activity' => $recentActivity,
                'period' => $period,
                'currency' => $currency,
            ]
        ]);
    }

    /**
     * Get users with pagination and filters
     */
    public function getUsers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive,suspended',
            'is_verified' => 'nullable|boolean',
            'role' => 'nullable|string|in:user,admin,super_admin',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|string|in:created_at,last_login,name,email',
            'order' => 'nullable|string|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $perPage = $request->get('per_page', 20);
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $query = User::with(['wallets'])
            ->withCount(['bets', 'transactions']);

        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $query->orderBy($sort, $order);

        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ]
            ]
        ]);
    }

    /**
     * Get specific user details
     */
    public function getUser(Request $request, $userId): JsonResponse
    {
        $user = User::with([
            'wallets',
            'bets.selections.match',
            'transactions' => function($q) {
                $q->latest()->limit(20);
            },
            'auditLogs' => function($q) {
                $q->latest()->limit(20);
            }
        ])
        ->withCount(['bets', 'transactions'])
        ->find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Calculate user statistics
        $stats = [
            'total_deposited' => $user->transactions()
                ->where('type', 'deposit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_withdrawn' => $user->transactions()
                ->where('type', 'withdrawal')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_bet' => $user->bets()->sum('stake'),
            'total_won' => $user->bets()
                ->where('status', 'won')
                ->sum('payout'),
            'win_rate' => $user->getWinRate(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,inactive,suspended',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $oldStatus = $user->status;
        $newStatus = $request->status;

        $user->update([
            'status' => $newStatus,
        ]);

        // Log the status change
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'user_status_changed',
            'model_type' => User::class,
            'model_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $request->reason,
                'target_user_id' => $user->id,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'data' => [
                'user' => $user->fresh()
            ]
        ]);
    }

    /**
     * Get bets with filters
     */
    public function getBets(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|string|in:pending,won,lost,void,cashout',
            'bet_type' => 'nullable|string|in:single,multiple,system',
            'is_live_bet' => 'nullable|boolean',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $perPage = $request->get('per_page', 20);

        $query = Bet::with([
            'user:id,name,email',
            'selections.match.sport',
            'selections.match.league'
        ])
        ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('bet_type')) {
            $query->where('bet_type', $request->bet_type);
        }

        if ($request->has('is_live_bet')) {
            $query->where('is_live_bet', $request->is_live_bet);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $bets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'bets' => $bets->items(),
                'pagination' => [
                    'current_page' => $bets->currentPage(),
                    'last_page' => $bets->lastPage(),
                    'per_page' => $bets->perPage(),
                    'total' => $bets->total(),
                ]
            ]
        ]);
    }

    /**
     * Get transactions with filters
     */
    public function getTransactions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|integer|exists:users,id',
            'type' => 'nullable|string|in:deposit,withdrawal,bet_placed,bet_won,bonus,cashback',
            'status' => 'nullable|string|in:pending,completed,failed,cancelled',
            'currency' => 'nullable|string|size:3',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $perPage = $request->get('per_page', 20);

        $query = Transaction::with(['user:id,name,email', 'wallet'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'transactions' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ]
            ]
        ]);
    }

    /**
     * Get system settings
     */
    public function getSettings(Request $request): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => $settings
            ]
        ]);
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($request->settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            // Log settings update
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'settings_updated',
                'model_type' => Setting::class,
                'model_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'updated_settings' => array_keys($request->settings),
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get audit logs
     */
    public function getAuditLogs(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|integer|exists:users,id',
            'action' => 'nullable|string',
            'model_type' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $perPage = $request->get('per_page', 20);

        $query = AuditLog::with('user:id,name,email')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ]
            ]
        ]);
    }
}
