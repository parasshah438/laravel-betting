<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * Get user wallets
     */
    public function index(Request $request): JsonResponse
    {
        $wallets = Wallet::where('user_id', $request->user()->id)
            ->with(['transactions' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'wallets' => $wallets
            ]
        ]);
    }

    /**
     * Get specific wallet
     */
    public function show(Request $request, $currency): JsonResponse
    {
        $wallet = Wallet::where('user_id', $request->user()->id)
            ->where('currency', strtoupper($currency))
            ->with(['transactions' => function($query) {
                $query->latest()->limit(10);
            }])
            ->first();

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'wallet' => $wallet
            ]
        ]);
    }

    /**
     * Create new wallet
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'required|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $currency = strtoupper($request->currency);
        $user = $request->user();

        // Check if wallet already exists
        $existingWallet = Wallet::where('user_id', $user->id)
            ->where('currency', $currency)
            ->first();

        if ($existingWallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet already exists for this currency'
            ], 400);
        }

        // Check if currency is supported
        $supportedCurrencies = Setting::get('supported_currencies', ['USD']);
        if (!in_array($currency, $supportedCurrencies)) {
            return response()->json([
                'success' => false,
                'message' => 'Currency not supported'
            ], 400);
        }

        try {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'currency' => $currency,
                'balance' => 0,
                'bonus_balance' => 0,
                'locked_balance' => 0,
                'withdrawable_balance' => 0,
            ]);

            // Log wallet creation
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'wallet_created',
                'model_type' => Wallet::class,
                'model_id' => $wallet->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => ['currency' => $currency]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wallet created successfully',
                'data' => [
                    'wallet' => $wallet
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deposit funds
     */
    public function deposit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|string|in:stripe,paypal,crypto,bank_transfer',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $amount = $request->amount;
        $currency = strtoupper($request->currency);
        $user = $request->user();

        // Check deposit limits
        $minDeposit = Setting::get('min_deposit_amount', 10);
        $maxDeposit = Setting::get('max_deposit_amount', 50000);

        if ($amount < $minDeposit) {
            return response()->json([
                'success' => false,
                'message' => "Minimum deposit amount is {$minDeposit} {$currency}"
            ], 400);
        }

        if ($amount > $maxDeposit) {
            return response()->json([
                'success' => false,
                'message' => "Maximum deposit amount is {$maxDeposit} {$currency}"
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Get or create wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id, 'currency' => $currency],
                [
                    'balance' => 0,
                    'bonus_balance' => 0,
                    'locked_balance' => 0,
                    'withdrawable_balance' => 0,
                ]
            );

            // Create pending transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => 'DEP_' . Str::random(12),
                'type' => 'deposit',
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance, // Will be updated when confirmed
                'currency' => $currency,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'description' => "Deposit via {$request->payment_method}",
            ]);

            // Log deposit initiation
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'deposit_initiated',
                'model_type' => Transaction::class,
                'model_id' => $transaction->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'amount' => $amount,
                    'currency' => $currency,
                    'payment_method' => $request->payment_method,
                ]
            ]);

            DB::commit();

            // Here you would integrate with your payment gateway
            // For now, we'll simulate a successful deposit
            $this->processDepositConfirmation($transaction);

            return response()->json([
                'success' => true,
                'message' => 'Deposit initiated successfully',
                'data' => [
                    'transaction' => $transaction->fresh()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Deposit failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw funds
     */
    public function withdraw(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|string|in:bank_transfer,crypto,paypal',
            'withdrawal_details' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $amount = $request->amount;
        $currency = strtoupper($request->currency);
        $user = $request->user();

        // Check withdrawal limits
        $minWithdrawal = Setting::get('min_withdrawal_amount', 20);
        $maxWithdrawal = Setting::get('max_withdrawal_amount', 25000);

        if ($amount < $minWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => "Minimum withdrawal amount is {$minWithdrawal} {$currency}"
            ], 400);
        }

        if ($amount > $maxWithdrawal) {
            return response()->json([
                'success' => false,
                'message' => "Maximum withdrawal amount is {$maxWithdrawal} {$currency}"
            ], 400);
        }

        try {
            DB::beginTransaction();

            $wallet = Wallet::where('user_id', $user->id)
                ->where('currency', $currency)
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found'
                ], 404);
            }

            // Check if user has sufficient withdrawable balance
            if (!$wallet->canWithdraw($amount)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient withdrawable balance'
                ], 400);
            }

            // Create withdrawal transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => 'WTH_' . Str::random(12),
                'type' => 'withdrawal',
                'amount' => $amount,
                'balance_before' => $wallet->withdrawable_balance,
                'balance_after' => $wallet->withdrawable_balance - $amount,
                'currency' => $currency,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'description' => "Withdrawal via {$request->payment_method}",
                'metadata' => [
                    'withdrawal_details' => $request->withdrawal_details
                ]
            ]);

            // Update wallet balance
            $wallet->subtractBalance($amount, 'withdrawable_balance');

            // Log withdrawal request
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'withdrawal_requested',
                'model_type' => Transaction::class,
                'model_id' => $transaction->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'amount' => $amount,
                    'currency' => $currency,
                    'payment_method' => $request->payment_method,
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'data' => [
                    'transaction' => $transaction
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction history
     */
    public function transactions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'currency' => 'nullable|string|size:3',
            'type' => 'nullable|string|in:deposit,withdrawal,bet_placed,bet_won,bet_lost,bet_refund,bonus,commission',
            'status' => 'nullable|string|in:pending,completed,failed,cancelled',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $perPage = $request->get('per_page', 20);

        $query = Transaction::where('user_id', $user->id)
            ->with(['wallet', 'bet'])
            ->orderBy('created_at', 'desc');

        if ($request->has('currency')) {
            $query->where('currency', strtoupper($request->currency));
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
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
     * Process deposit confirmation (simulate payment gateway callback)
     */
    private function processDepositConfirmation(Transaction $transaction): void
    {
        try {
            DB::beginTransaction();

            $wallet = $transaction->wallet;
            
            // Update wallet balance
            $wallet->addBalance($transaction->amount);
            $wallet->addBalance($transaction->amount, 'withdrawable_balance');

            // Update transaction
            $transaction->update([
                'status' => 'completed',
                'balance_after' => $wallet->balance,
                'processed_at' => now(),
            ]);

            // Log deposit confirmation
            AuditLog::create([
                'user_id' => $transaction->user_id,
                'action' => 'deposit_completed',
                'model_type' => Transaction::class,
                'model_id' => $transaction->id,
                'metadata' => [
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                ]
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
