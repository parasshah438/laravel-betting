<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BettingController extends Controller
{
    /**
     * Show the live betting page
     */
    public function live()
    {
        $liveMatches = $this->getLiveMatches();
        $liveSports = $this->getLiveSports();
        
        return view('betting.live', compact('liveMatches', 'liveSports'));
    }
    
    /**
     * Show betting history
     */
    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $bets = $this->getUserBets();
        $statistics = $this->getBettingStatistics();
        
        return view('betting.history', compact('bets', 'statistics'));
    }
    
    /**
     * Show bet details
     */
    public function betDetails($betId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $bet = $this->getBetDetails($betId);
        
        return view('betting.bet-details', compact('bet'));
    }
    
    /**
     * Place a bet
     */
    public function placeBet(Request $request)
    {
        $request->validate([
            'selections' => 'required|array',
            'stake' => 'required|numeric|min:1',
            'bet_type' => 'required|in:single,multiple,system'
        ]);
        
        // Place bet logic here
        $bet = $this->processBet($request->all());
        
        return response()->json([
            'success' => true,
            'bet_id' => $bet['id'],
            'message' => 'Bet placed successfully!'
        ]);
    }
    
    /**
     * Cash out a bet
     */
    public function cashOut($betId)
    {
        // Cash out logic here
        $result = $this->processCashOut($betId);
        
        return response()->json([
            'success' => $result['success'],
            'amount' => $result['amount'] ?? 0,
            'message' => $result['message']
        ]);
    }
    
    /**
     * Get live odds for a match
     */
    public function getOdds($matchId)
    {
        $odds = $this->fetchLiveOdds($matchId);
        
        return response()->json($odds);
    }
    
    /**
     * Get live matches
     */
    public function getLiveMatches()
    {
        return [
            [
                'id' => 1,
                'home_team' => 'Chelsea',
                'away_team' => 'Arsenal',
                'league' => 'Premier League',
                'minute' => 73,
                'home_score' => 1,
                'away_score' => 2,
                'status' => '2nd Half',
                'home_odds' => 4.20,
                'draw_odds' => 3.80,
                'away_odds' => 1.70
            ],
            [
                'id' => 2,
                'home_team' => 'LA Lakers',
                'away_team' => 'Boston Celtics',
                'league' => 'NBA',
                'quarter' => 'Q3 8:42',
                'home_score' => 89,
                'away_score' => 92,
                'status' => '3rd Quarter',
                'home_odds' => 2.30,
                'away_odds' => 1.60
            ]
        ];
    }
    
    /**
     * Get live sports with match counts
     */
    private function getLiveSports()
    {
        return [
            ['name' => 'All Live', 'count' => 8, 'active' => true],
            ['name' => 'Football', 'count' => 4, 'icon' => 'bi-dribbble'],
            ['name' => 'Basketball', 'count' => 2, 'icon' => 'bi-basketball'],
            ['name' => 'Tennis', 'count' => 2, 'icon' => 'bi-tennis-ball']
        ];
    }
    
    /**
     * Get user's betting history
     */
    private function getUserBets()
    {
        if (!Auth::check()) return [];
        
        return [
            [
                'id' => 1,
                'date' => now(),
                'event' => 'Chelsea vs Arsenal',
                'selection' => 'Arsenal Win',
                'odds' => 1.70,
                'stake' => 50.00,
                'potential_return' => 85.50,
                'status' => 'won',
                'profit' => 35.50
            ],
            [
                'id' => 2,
                'date' => now()->subHours(2),
                'event' => 'Lakers vs Celtics',
                'selection' => 'Over 185.5',
                'odds' => 1.90,
                'stake' => 50.00,
                'potential_return' => 95.00,
                'status' => 'pending'
            ]
        ];
    }
    
    /**
     * Get betting statistics for user
     */
    private function getBettingStatistics()
    {
        if (!Auth::check()) return [];
        
        return [
            'total_bets' => 1247,
            'bets_won' => 853,
            'bets_lost' => 394,
            'win_rate' => 68.4,
            'total_wagered' => 12567.50,
            'total_returns' => 13842.25,
            'net_profit' => 1274.75,
            'roi' => 10.15
        ];
    }
    
    /**
     * Get bet details
     */
    private function getBetDetails($betId)
    {
        return [
            'id' => $betId,
            'event' => 'Chelsea vs Arsenal',
            'league' => 'Premier League',
            'selection' => 'Arsenal Win',
            'odds' => 1.70,
            'stake' => 50.00,
            'potential_return' => 85.50,
            'status' => 'won',
            'placed_at' => now(),
            'settled_at' => now()->addHours(2)
        ];
    }
    
    /**
     * Process bet placement
     */
    private function processBet($betData)
    {
        // Simulate bet processing
        return [
            'id' => rand(1000, 9999),
            'status' => 'pending',
            'message' => 'Bet placed successfully!'
        ];
    }
    
    /**
     * Process cash out
     */
    private function processCashOut($betId)
    {
        // Simulate cash out processing
        return [
            'success' => true,
            'amount' => 42.50,
            'message' => 'Cash out successful!'
        ];
    }
    
    /**
     * Fetch live odds for match
     */
    private function fetchLiveOdds($matchId)
    {
        // Simulate live odds
        return [
            'match_id' => $matchId,
            'home_odds' => 2.10 + (rand(-20, 20) / 100),
            'draw_odds' => 3.40 + (rand(-30, 30) / 100),
            'away_odds' => 1.70 + (rand(-15, 15) / 100),
            'updated_at' => now()
        ];
    }
    
    /**
     * Add selection to bet slip
     */
    public function addToBetSlip(Request $request)
    {
        $request->validate([
            'match_id' => 'required',
            'selection' => 'required',
            'odds' => 'required|numeric'
        ]);
        
        // Add to session bet slip
        $betSlip = session()->get('bet_slip', []);
        $betSlip[] = $request->all();
        session()->put('bet_slip', $betSlip);
        
        return response()->json([
            'success' => true,
            'count' => count($betSlip)
        ]);
    }
    
    /**
     * Remove selection from bet slip
     */
    public function removeFromBetSlip($index)
    {
        $betSlip = session()->get('bet_slip', []);
        unset($betSlip[$index]);
        $betSlip = array_values($betSlip);
        session()->put('bet_slip', $betSlip);
        
        return response()->json([
            'success' => true,
            'count' => count($betSlip)
        ]);
    }
    
    /**
     * Get current bet slip
     */
    public function getBetSlip()
    {
        $betSlip = session()->get('bet_slip', []);
        
        return response()->json([
            'selections' => $betSlip,
            'count' => count($betSlip),
            'total_odds' => $this->calculateTotalOdds($betSlip)
        ]);
    }
    
    /**
     * Calculate total odds for bet slip
     */
    private function calculateTotalOdds($selections)
    {
        if (empty($selections)) return 0;
        
        $totalOdds = 1;
        foreach ($selections as $selection) {
            $totalOdds *= $selection['odds'];
        }
        
        return round($totalOdds, 2);
    }
}
