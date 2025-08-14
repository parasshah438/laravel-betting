<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Match;
use App\Models\Sport;
use App\Models\League;
use App\Models\Team;
use App\Models\BetMarket;
use App\Models\BetOption;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MatchManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin,super_admin']);
    }

    /**
     * Get matches with filters for admin
     */
    public function getMatches(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sport_id' => 'nullable|integer|exists:sports,id',
            'league_id' => 'nullable|integer|exists:leagues,id',
            'status' => 'nullable|string|in:scheduled,live,finished,cancelled,postponed',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'search' => 'nullable|string|max:255',
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

        $query = Match::with([
            'sport',
            'league',
            'homeTeam',
            'awayTeam',
            'betMarkets.betOptions'
        ])
        ->withCount(['betOptions', 'bets'])
        ->orderBy('start_time', 'desc');

        // Apply filters
        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('start_time', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('start_time', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('homeTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('awayTeam', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('league', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $matches = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'matches' => $matches->items(),
                'pagination' => [
                    'current_page' => $matches->currentPage(),
                    'last_page' => $matches->lastPage(),
                    'per_page' => $matches->perPage(),
                    'total' => $matches->total(),
                ]
            ]
        ]);
    }

    /**
     * Create new match
     */
    public function createMatch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sport_id' => 'required|integer|exists:sports,id',
            'league_id' => 'required|integer|exists:leagues,id',
            'home_team_id' => 'required|integer|exists:teams,id',
            'away_team_id' => 'required|integer|exists:teams,id|different:home_team_id',
            'start_time' => 'required|date|after:now',
            'venue' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'bet_markets' => 'nullable|array',
            'bet_markets.*.name' => 'required|string|max:255',
            'bet_markets.*.type' => 'required|string',
            'bet_markets.*.options' => 'required|array|min:2',
            'bet_markets.*.options.*.name' => 'required|string|max:255',
            'bet_markets.*.options.*.odds' => 'required|numeric|min:1.01',
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

            // Create match
            $match = Match::create([
                'sport_id' => $request->sport_id,
                'league_id' => $request->league_id,
                'home_team_id' => $request->home_team_id,
                'away_team_id' => $request->away_team_id,
                'start_time' => $request->start_time,
                'venue' => $request->venue,
                'status' => 'scheduled',
                'is_featured' => $request->get('is_featured', false),
            ]);

            // Create bet markets and options if provided
            if ($request->has('bet_markets')) {
                foreach ($request->bet_markets as $marketData) {
                    $betMarket = BetMarket::create([
                        'match_id' => $match->id,
                        'name' => $marketData['name'],
                        'type' => $marketData['type'],
                        'is_active' => true,
                    ]);

                    foreach ($marketData['options'] as $optionData) {
                        BetOption::create([
                            'match_id' => $match->id,
                            'bet_market_id' => $betMarket->id,
                            'name' => $optionData['name'],
                            'odds' => $optionData['odds'],
                            'is_active' => true,
                        ]);
                    }
                }
            }

            // Log match creation
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'match_created',
                'model_type' => Match::class,
                'model_id' => $match->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'match_details' => $match->getMatchDisplayName(),
                    'start_time' => $match->start_time,
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Match created successfully',
                'data' => [
                    'match' => $match->load([
                        'sport',
                        'league',
                        'homeTeam',
                        'awayTeam',
                        'betMarkets.betOptions'
                    ])
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create match',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update match
     */
    public function updateMatch(Request $request, $matchId): JsonResponse
    {
        $match = Match::find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'start_time' => 'nullable|date',
            'venue' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:scheduled,live,finished,cancelled,postponed',
            'is_featured' => 'nullable|boolean',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'match_data' => 'nullable|array',
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

            $oldData = $match->toArray();

            $match->update($request->only([
                'start_time',
                'venue',
                'status',
                'is_featured',
                'home_score',
                'away_score',
                'match_data'
            ]));

            // If match is finished, settle bets
            if ($request->status === 'finished' && $oldData['status'] !== 'finished') {
                $this->settleMatchBets($match);
            }

            // Log match update
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'match_updated',
                'model_type' => Match::class,
                'model_id' => $match->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'match_details' => $match->getMatchDisplayName(),
                    'changes' => array_diff_assoc($match->toArray(), $oldData),
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Match updated successfully',
                'data' => [
                    'match' => $match->fresh()->load([
                        'sport',
                        'league',
                        'homeTeam',
                        'awayTeam',
                        'betMarkets.betOptions'
                    ])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update match',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get match betting options
     */
    public function getMatchBettingOptions(Request $request, $matchId): JsonResponse
    {
        $match = Match::with([
            'betMarkets.betOptions' => function($query) {
                $query->orderBy('created_at');
            }
        ])->find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'match' => $match,
                'bet_markets' => $match->betMarkets
            ]
        ]);
    }

    /**
     * Add betting market to match
     */
    public function addBettingMarket(Request $request, $matchId): JsonResponse
    {
        $match = Match::find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*.name' => 'required|string|max:255',
            'options.*.odds' => 'required|numeric|min:1.01',
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

            $betMarket = BetMarket::create([
                'match_id' => $match->id,
                'name' => $request->name,
                'type' => $request->type,
                'is_active' => true,
            ]);

            foreach ($request->options as $optionData) {
                BetOption::create([
                    'match_id' => $match->id,
                    'bet_market_id' => $betMarket->id,
                    'name' => $optionData['name'],
                    'odds' => $optionData['odds'],
                    'is_active' => true,
                ]);
            }

            // Log betting market addition
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'betting_market_added',
                'model_type' => BetMarket::class,
                'model_id' => $betMarket->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'match_details' => $match->getMatchDisplayName(),
                    'market_name' => $betMarket->name,
                    'options_count' => count($request->options),
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Betting market added successfully',
                'data' => [
                    'bet_market' => $betMarket->load('betOptions')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add betting market',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update betting odds
     */
    public function updateOdds(Request $request, $optionId): JsonResponse
    {
        $betOption = BetOption::find($optionId);

        if (!$betOption) {
            return response()->json([
                'success' => false,
                'message' => 'Bet option not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'odds' => 'required|numeric|min:1.01',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldOdds = $betOption->odds;
        $newOdds = $request->odds;

        $betOption->update([
            'odds' => $newOdds,
            'is_active' => $request->get('is_active', $betOption->is_active),
        ]);

        // Log odds update
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'odds_updated',
            'model_type' => BetOption::class,
            'model_id' => $betOption->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'option_name' => $betOption->name,
                'old_odds' => $oldOdds,
                'new_odds' => $newOdds,
                'match_details' => $betOption->match->getMatchDisplayName(),
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Odds updated successfully',
            'data' => [
                'bet_option' => $betOption->fresh()
            ]
        ]);
    }

    /**
     * Suspend/Resume betting on match or specific option
     */
    public function toggleBetting(Request $request, $matchId): JsonResponse
    {
        $match = Match::find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'action' => 'required|string|in:suspend,resume',
            'option_id' => 'nullable|integer|exists:bet_options,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $action = $request->action;
        $isActive = $action === 'resume';

        if ($request->has('option_id')) {
            // Toggle specific betting option
            $betOption = BetOption::find($request->option_id);
            $betOption->update(['is_active' => $isActive]);
            
            $logMessage = "Betting option {$action}ed";
            $metadata = [
                'option_name' => $betOption->name,
                'match_details' => $match->getMatchDisplayName(),
            ];
        } else {
            // Toggle all betting options for the match
            $match->betOptions()->update(['is_active' => $isActive]);
            
            $logMessage = "Match betting {$action}ed";
            $metadata = [
                'match_details' => $match->getMatchDisplayName(),
            ];
        }

        // Log betting toggle
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'betting_toggled',
            'model_type' => Match::class,
            'model_id' => $match->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => array_merge($metadata, ['action' => $action])
        ]);

        return response()->json([
            'success' => true,
            'message' => $logMessage . ' successfully'
        ]);
    }

    /**
     * Settle match bets (called automatically when match status changes to finished)
     */
    private function settleMatchBets(Match $match)
    {
        // This method would contain the logic to settle all bets for a finished match
        // It would check the match result and settle each bet selection accordingly
        
        // For now, we'll just mark it as a placeholder
        // In a real implementation, this would:
        // 1. Get all pending bets for this match
        // 2. Determine winning selections based on match result
        // 3. Update bet selection statuses
        // 4. Calculate payouts for winning bets
        // 5. Update user balances
        // 6. Create payout transactions
        
        \Log::info("Settling bets for match: {$match->getMatchDisplayName()}");
    }
}
