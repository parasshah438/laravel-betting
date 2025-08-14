<?php

namespace App\Http\Controllers\Api\Betting;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\BetSelection;
use App\Models\BetOption;
use App\Models\Match;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BettingController extends Controller
{
    /**
     * Place a bet
     */
    public function placeBet(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'selections' => 'required|array|min:1|max:20',
            'selections.*.bet_option_id' => 'required|integer|exists:bet_options,id',
            'selections.*.odds' => 'required|numeric|min:1.01',
            'stake' => 'required|numeric|min:0.01',
            'bet_type' => 'required|string|in:single,multiple,system',
            'system_config' => 'nullable|array',
            'currency' => 'required|string|size:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $stake = $request->stake;
        $currency = strtoupper($request->currency);
        $betType = $request->bet_type;
        $selections = $request->selections;

        // Validate bet type and selections count
        if ($betType === 'single' && count($selections) !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Single bet must have exactly one selection'
            ], 400);
        }

        if ($betType === 'multiple' && count($selections) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Multiple bet must have at least 2 selections'
            ], 400);
        }

        // Check betting limits
        $minBet = Setting::get('min_bet_amount', 1);
        $maxBet = Setting::get('max_bet_amount', 10000);

        if ($stake < $minBet) {
            return response()->json([
                'success' => false,
                'message' => "Minimum bet amount is {$minBet} {$currency}"
            ], 400);
        }

        if ($stake > $maxBet) {
            return response()->json([
                'success' => false,
                'message' => "Maximum bet amount is {$maxBet} {$currency}"
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Get user wallet
            $wallet = Wallet::where('user_id', $user->id)
                ->where('currency', $currency)
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found'
                ], 404);
            }

            // Check if user has sufficient balance
            if (!$wallet->canBet($stake)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance'
                ], 400);
            }

            // Validate bet options and calculate total odds
            $totalOdds = 1;
            $validSelections = [];
            $isLiveBet = false;

            foreach ($selections as $selection) {
                $betOption = BetOption::with(['match', 'betMarket'])
                    ->find($selection['bet_option_id']);

                if (!$betOption) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid bet option'
                    ], 400);
                }

                if (!$betOption->canBet()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Betting not available for: {$betOption->name}"
                    ], 400);
                }

                // Check if odds have changed significantly
                $oddsThreshold = 0.05; // 5% threshold
                if (abs($betOption->odds - $selection['odds']) > $oddsThreshold) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Odds have changed. Please refresh and try again.',
                        'data' => [
                            'option' => $betOption->name,
                            'current_odds' => $betOption->odds,
                            'requested_odds' => $selection['odds']
                        ]
                    ], 400);
                }

                if ($betOption->match->isLive()) {
                    $isLiveBet = true;
                }

                $totalOdds *= $betOption->odds;

                $validSelections[] = [
                    'bet_option' => $betOption,
                    'odds' => $betOption->odds
                ];
            }

            // Calculate potential win
            $potentialWin = $stake * $totalOdds;

            // Check maximum win limit
            $maxWin = Setting::get('max_win_amount', 100000);
            if ($potentialWin > $maxWin) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum win amount is {$maxWin} {$currency}"
                ], 400);
            }

            // Lock the stake amount in user's wallet
            $wallet->lockBalance($stake);

            // Create bet record
            $bet = Bet::create([
                'user_id' => $user->id,
                'bet_id' => 'BET_' . Str::random(12),
                'bet_type' => $betType,
                'stake' => $stake,
                'potential_win' => $potentialWin,
                'total_odds' => $totalOdds,
                'status' => 'pending',
                'currency' => $currency,
                'is_live_bet' => $isLiveBet,
                'is_system_bet' => $betType === 'system',
                'system_config' => $request->system_config,
            ]);

            // Create bet selections
            foreach ($validSelections as $selection) {
                $betOption = $selection['bet_option'];
                
                BetSelection::create([
                    'bet_id' => $bet->id,
                    'match_id' => $betOption->match_id,
                    'bet_option_id' => $betOption->id,
                    'selection_name' => $betOption->name,
                    'odds' => $selection['odds'],
                    'status' => 'pending',
                    'match_info' => [
                        'match_name' => $betOption->match->getMatchDisplayName(),
                        'league_name' => $betOption->match->league->name,
                        'sport_name' => $betOption->match->sport->name,
                        'start_time' => $betOption->match->start_time,
                        'market_name' => $betOption->betMarket->name,
                    ]
                ]);

                // Update bet option statistics
                $betOption->incrementBetCount($stake);
            }

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => 'BET_' . Str::random(12),
                'type' => 'bet_placed',
                'amount' => $stake,
                'balance_before' => $wallet->balance + $stake, // Before locking
                'balance_after' => $wallet->balance,
                'currency' => $currency,
                'status' => 'completed',
                'description' => "Bet placed - {$bet->bet_id}",
                'related_bet_id' => $bet->id,
                'processed_at' => now(),
            ]);

            // Log bet placement
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'bet_placed',
                'model_type' => Bet::class,
                'model_id' => $bet->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'bet_type' => $betType,
                    'stake' => $stake,
                    'currency' => $currency,
                    'potential_win' => $potentialWin,
                    'selections_count' => count($selections),
                    'is_live_bet' => $isLiveBet,
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bet placed successfully',
                'data' => [
                    'bet' => $bet->load(['selections.match', 'selections.betOption.betMarket'])
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bet placement failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's bets
     */
    public function getUserBets(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string|in:pending,won,lost,void,cashout',
            'bet_type' => 'nullable|string|in:single,multiple,system',
            'is_live_bet' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
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

        $query = Bet::where('user_id', $user->id)
            ->with([
                'selections.match.sport',
                'selections.match.league',
                'selections.match.homeTeam',
                'selections.match.awayTeam',
                'selections.betOption.betMarket'
            ])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('bet_type')) {
            $query->where('bet_type', $request->bet_type);
        }

        if ($request->has('is_live_bet')) {
            $query->where('is_live_bet', $request->is_live_bet);
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
     * Get specific bet details
     */
    public function getBet(Request $request, $betId): JsonResponse
    {
        $bet = Bet::where('bet_id', $betId)
            ->where('user_id', $request->user()->id)
            ->with([
                'selections.match.sport',
                'selections.match.league',
                'selections.match.homeTeam',
                'selections.match.awayTeam',
                'selections.betOption.betMarket',
                'transactions'
            ])
            ->first();

        if (!$bet) {
            return response()->json([
                'success' => false,
                'message' => 'Bet not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bet' => $bet
            ]
        ]);
    }

    /**
     * Cash out bet
     */
    public function cashOut(Request $request, $betId): JsonResponse
    {
        $bet = Bet::where('bet_id', $betId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$bet) {
            return response()->json([
                'success' => false,
                'message' => 'Bet not found'
            ], 404);
        }

        if (!$bet->canCashout()) {
            return response()->json([
                'success' => false,
                'message' => 'Cash out not available for this bet'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();
            $wallet = Wallet::where('user_id', $user->id)
                ->where('currency', $bet->currency)
                ->first();

            if (!$wallet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found'
                ], 404);
            }

            $cashoutAmount = $bet->cashout_value;

            // Update bet status
            $bet->settle('cashout', $cashoutAmount);

            // Unlock the original stake
            $wallet->unlockBalance($bet->stake);

            // Add cashout amount to balance
            $wallet->addBalance($cashoutAmount);
            $wallet->addBalance($cashoutAmount, 'withdrawable_balance');

            // Create cashout transaction
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'transaction_id' => 'CASHOUT_' . Str::random(12),
                'type' => 'bet_cashout',
                'amount' => $cashoutAmount,
                'balance_before' => $wallet->balance - $cashoutAmount,
                'balance_after' => $wallet->balance,
                'currency' => $bet->currency,
                'status' => 'completed',
                'description' => "Cash out - {$bet->bet_id}",
                'related_bet_id' => $bet->id,
                'processed_at' => now(),
            ]);

            // Log cashout
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'bet_cashout',
                'model_type' => Bet::class,
                'model_id' => $bet->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'cashout_amount' => $cashoutAmount,
                    'original_stake' => $bet->stake,
                    'potential_win' => $bet->potential_win,
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bet cashed out successfully',
                'data' => [
                    'bet' => $bet->fresh(),
                    'cashout_amount' => $cashoutAmount,
                    'new_balance' => $wallet->fresh()->balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Cash out failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get betting statistics
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'total_bets' => Bet::where('user_id', $user->id)->count(),
            'pending_bets' => Bet::where('user_id', $user->id)->pending()->count(),
            'won_bets' => Bet::where('user_id', $user->id)->won()->count(),
            'lost_bets' => Bet::where('user_id', $user->id)->lost()->count(),
            'total_staked' => Bet::where('user_id', $user->id)->sum('stake'),
            'total_winnings' => Bet::where('user_id', $user->id)->won()->sum('payout'),
            'biggest_win' => Bet::where('user_id', $user->id)->won()->max('payout'),
            'average_odds' => Bet::where('user_id', $user->id)->avg('total_odds'),
        ];

        // Calculate win rate
        $totalSettledBets = $stats['won_bets'] + $stats['lost_bets'];
        $stats['win_rate'] = $totalSettledBets > 0 ? ($stats['won_bets'] / $totalSettledBets) * 100 : 0;

        // Calculate profit/loss
        $stats['profit_loss'] = $stats['total_winnings'] - $stats['total_staked'];

        // Recent activity (last 30 days)
        $recentStats = [
            'recent_bets' => Bet::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'recent_winnings' => Bet::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->won()
                ->sum('payout'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'overall_stats' => $stats,
                'recent_stats' => $recentStats
            ]
        ]);
    }
}
