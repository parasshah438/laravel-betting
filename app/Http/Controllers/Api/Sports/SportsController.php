<?php

namespace App\Http\Controllers\Api\Sports;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\League;
use App\Models\Team;
use App\Models\Match;
use App\Models\BetOption;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SportsController extends Controller
{
    /**
     * Get all sports
     */
    public function index(Request $request): JsonResponse
    {
        $sports = Sport::active()
            ->ordered()
            ->withCount(['leagues', 'matches' => function($query) {
                $query->where('start_time', '>', now())
                    ->where('status', 'scheduled');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sports' => $sports
            ]
        ]);
    }

    /**
     * Get specific sport with leagues
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $sport = Sport::where('slug', $slug)
            ->active()
            ->with([
                'leagues' => function($query) {
                    $query->active()->ordered();
                }
            ])
            ->first();

        if (!$sport) {
            return response()->json([
                'success' => false,
                'message' => 'Sport not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sport' => $sport
            ]
        ]);
    }

    /**
     * Get leagues for a sport
     */
    public function leagues(Request $request, string $sportSlug): JsonResponse
    {
        $sport = Sport::where('slug', $sportSlug)->active()->first();

        if (!$sport) {
            return response()->json([
                'success' => false,
                'message' => 'Sport not found'
            ], 404);
        }

        $leagues = League::where('sport_id', $sport->id)
            ->active()
            ->ordered()
            ->withCount(['matches' => function($query) {
                $query->where('start_time', '>', now())
                    ->where('status', 'scheduled');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sport' => $sport,
                'leagues' => $leagues
            ]
        ]);
    }

    /**
     * Get teams for a sport
     */
    public function teams(Request $request, string $sportSlug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'country' => 'nullable|string|size:2',
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

        $sport = Sport::where('slug', $sportSlug)->active()->first();

        if (!$sport) {
            return response()->json([
                'success' => false,
                'message' => 'Sport not found'
            ], 404);
        }

        $query = Team::where('sport_id', $sport->id)->active();

        if ($request->has('country')) {
            $query->where('country', strtoupper($request->country));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $teams = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'sport' => $sport,
                'teams' => $teams->items(),
                'pagination' => [
                    'current_page' => $teams->currentPage(),
                    'last_page' => $teams->lastPage(),
                    'per_page' => $teams->perPage(),
                    'total' => $teams->total(),
                ]
            ]
        ]);
    }

    /**
     * Get matches for a sport
     */
    public function matches(Request $request, string $sportSlug): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'league_id' => 'nullable|integer|exists:leagues,id',
            'status' => 'nullable|string|in:scheduled,live,halftime,finished',
            'date' => 'nullable|date',
            'featured' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $sport = Sport::where('slug', $sportSlug)->active()->first();

        if (!$sport) {
            return response()->json([
                'success' => false,
                'message' => 'Sport not found'
            ], 404);
        }

        $query = Match::where('sport_id', $sport->id)
            ->with(['league', 'homeTeam', 'awayTeam'])
            ->active();

        if ($request->has('league_id')) {
            $query->where('league_id', $request->league_id);
        }

        if ($request->has('status')) {
            if ($request->status === 'live') {
                $query->whereIn('status', ['live', 'halftime']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('date')) {
            $query->whereDate('start_time', $request->date);
        }

        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        $perPage = $request->get('per_page', 20);
        
        // Order by status priority and start time
        $matches = $query->orderByRaw("
            CASE status 
                WHEN 'live' THEN 1
                WHEN 'halftime' THEN 2
                WHEN 'scheduled' THEN 3
                WHEN 'finished' THEN 4
                ELSE 5
            END
        ")->orderBy('start_time')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'sport' => $sport,
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
     * Get popular/featured sports
     */
    public function popular(Request $request): JsonResponse
    {
        $sports = Sport::active()
            ->withCount(['matches' => function($query) {
                $query->where('start_time', '>', now())
                    ->where('start_time', '<', now()->addDays(7))
                    ->where('status', 'scheduled');
            }])
            ->orderBy('matches_count', 'desc')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sports' => $sports
            ]
        ]);
    }

    /**
     * Search sports, leagues, teams, matches
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
            'type' => 'nullable|string|in:sports,leagues,teams,matches',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $searchQuery = $request->query;
        $type = $request->get('type');

        $results = [];

        if (!$type || $type === 'sports') {
            $results['sports'] = Sport::active()
                ->where('name', 'like', "%{$searchQuery}%")
                ->limit(5)
                ->get(['id', 'name', 'slug']);
        }

        if (!$type || $type === 'leagues') {
            $results['leagues'] = League::active()
                ->where('name', 'like', "%{$searchQuery}%")
                ->with('sport:id,name,slug')
                ->limit(5)
                ->get(['id', 'name', 'slug', 'sport_id', 'country']);
        }

        if (!$type || $type === 'teams') {
            $results['teams'] = Team::active()
                ->where(function($q) use ($searchQuery) {
                    $q->where('name', 'like', "%{$searchQuery}%")
                      ->orWhere('short_name', 'like', "%{$searchQuery}%");
                })
                ->with('sport:id,name,slug')
                ->limit(5)
                ->get(['id', 'name', 'short_name', 'slug', 'sport_id', 'country']);
        }

        if (!$type || $type === 'matches') {
            $results['matches'] = Match::active()
                ->where('match_name', 'like', "%{$searchQuery}%")
                ->where('start_time', '>', now())
                ->with(['sport:id,name,slug', 'league:id,name,slug', 'homeTeam:id,name,short_name', 'awayTeam:id,name,short_name'])
                ->orderBy('start_time')
                ->limit(5)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'query' => $searchQuery,
                'results' => $results
            ]
        ]);
    }
}
