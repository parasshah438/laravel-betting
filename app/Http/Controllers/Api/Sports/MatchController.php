<?php

namespace App\Http\Controllers\Api\Sports;

use App\Http\Controllers\Controller;
use App\Models\Match;
use App\Models\BetOption;
use App\Models\BetMarket;
use App\Models\LiveEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{
    /**
     * Get match details with betting options
     */
    public function show(Request $request, int $matchId): JsonResponse
    {
        $match = Match::with([
            'sport',
            'league',
            'homeTeam',
            'awayTeam',
            'betOptions' => function($query) {
                $query->active()
                    ->with('betMarket')
                    ->orderBy('bet_market_id')
                    ->orderBy('odds');
            },
            'liveEvents' => function($query) {
                $query->orderBy('minute', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(10);
            }
        ])->find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found'
            ], 404);
        }

        // Group betting options by market
        $bettingMarkets = $match->betOptions
            ->groupBy('bet_market_id')
            ->map(function ($options, $marketId) {
                return [
                    'market' => $options->first()->betMarket,
                    'options' => $options->values()
                ];
            })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'match' => $match,
                'betting_markets' => $bettingMarkets,
                'can_bet' => $match->canPlaceBets(),
                'match_status' => [
                    'is_live' => $match->isLive(),
                    'is_scheduled' => $match->isScheduled(),
                    'is_finished' => $match->isFinished(),
                    'live_betting_enabled' => $match->live_betting_enabled,
                ]
            ]
        ]);
    }

    /**
     * Get live matches
     */
    public function live(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sport_id' => 'nullable|integer|exists:sports,id',
            'league_id' => 'nullable|integer|exists:leagues,id',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Match::live()
            ->with([
                'sport:id,name,slug',
                'league:id,name,slug',
                'homeTeam:id,name,short_name',
                'awayTeam:id,name,short_name'
            ]);

        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        $perPage = $request->get('per_page', 20);
        $matches = $query->orderBy('start_time')->paginate($perPage);

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
     * Get upcoming matches
     */
    public function upcoming(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sport_id' => 'nullable|integer|exists:sports,id',
            'league_id' => 'nullable|integer|exists:leagues,id',
            'days' => 'nullable|integer|min:1|max:30',
            'featured' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $days = $request->get('days', 7);
        
        $query = Match::upcoming()
            ->where('start_time', '<=', now()->addDays($days))
            ->with([
                'sport:id,name,slug',
                'league:id,name,slug',
                'homeTeam:id,name,short_name',
                'awayTeam:id,name,short_name'
            ]);

        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        $perPage = $request->get('per_page', 20);
        $matches = $query->orderBy('start_time')->paginate($perPage);

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
     * Get finished matches
     */
    public function finished(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sport_id' => 'nullable|integer|exists:sports,id',
            'league_id' => 'nullable|integer|exists:leagues,id',
            'days' => 'nullable|integer|min:1|max:30',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $days = $request->get('days', 7);
        
        $query = Match::finished()
            ->where('finished_at', '>=', now()->subDays($days))
            ->with([
                'sport:id,name,slug',
                'league:id,name,slug',
                'homeTeam:id,name,short_name',
                'awayTeam:id,name,short_name'
            ]);

        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        $perPage = $request->get('per_page', 20);
        $matches = $query->orderBy('finished_at', 'desc')->paginate($perPage);

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
     * Get match betting options
     */
    public function bettingOptions(Request $request, int $matchId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'market_key' => 'nullable|string|exists:bet_markets,key',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $match = Match::find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found'
            ], 404);
        }

        if (!$match->canPlaceBets()) {
            return response()->json([
                'success' => false,
                'message' => 'Betting is not available for this match'
            ], 400);
        }

        $query = BetOption::where('match_id', $matchId)
            ->active()
            ->with(['betMarket']);

        if ($request->has('market_key')) {
            $query->whereHas('betMarket', function($q) use ($request) {
                $q->where('key', $request->market_key);
            });
        }

        $options = $query->orderBy('bet_market_id')
            ->orderBy('odds')
            ->get();

        // Group by market
        $markets = $options->groupBy('bet_market_id')->map(function ($options, $marketId) {
            return [
                'market' => $options->first()->betMarket,
                'options' => $options->values()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'match' => $match->only(['id', 'match_name', 'start_time', 'status']),
                'betting_markets' => $markets,
                'can_bet' => $match->canPlaceBets(),
            ]
        ]);
    }

    /**
     * Get live events for a match
     */
    public function liveEvents(Request $request, int $matchId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'nullable|string|in:goal,yellow_card,red_card,substitution,corner,free_kick,offside',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $match = Match::find($matchId);

        if (!$match) {
            return response()->json([
                'success' => false,
                'message' => 'Match not found'
            ], 404);
        }

        $query = LiveEvent::where('match_id', $matchId);

        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        $limit = $request->get('limit', 50);
        $events = $query->orderBy('minute', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'match' => $match->only(['id', 'match_name', 'status', 'start_time']),
                'events' => $events,
                'event_count' => $events->count(),
            ]
        ]);
    }

    /**
     * Get today's matches
     */
    public function today(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sport_id' => 'nullable|integer|exists:sports,id',
            'status' => 'nullable|string|in:scheduled,live,finished',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Match::today()
            ->with([
                'sport:id,name,slug',
                'league:id,name,slug',
                'homeTeam:id,name,short_name',
                'awayTeam:id,name,short_name'
            ]);

        if ($request->has('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->has('status')) {
            if ($request->status === 'live') {
                $query->whereIn('status', ['live', 'halftime']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $matches = $query->orderByRaw("
            CASE status 
                WHEN 'live' THEN 1
                WHEN 'halftime' THEN 2
                WHEN 'scheduled' THEN 3
                WHEN 'finished' THEN 4
                ELSE 5
            END
        ")->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'date' => today()->toDateString(),
                'matches' => $matches,
                'summary' => [
                    'total' => $matches->count(),
                    'live' => $matches->whereIn('status', ['live', 'halftime'])->count(),
                    'scheduled' => $matches->where('status', 'scheduled')->count(),
                    'finished' => $matches->where('status', 'finished')->count(),
                ]
            ]
        ]);
    }
}
