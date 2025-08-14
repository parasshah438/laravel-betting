<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the betting platform homepage
     */
    public function index()
    {
        // Get featured matches, popular sports, promotions, etc.
        $featuredMatches = $this->getFeaturedMatches();
        $popularSports = $this->getPopularSports();
        $liveMatches = $this->getLiveMatches();
        $promotions = $this->getActivePromotions();
        
        return view('betting.home', compact(
            'featuredMatches',
            'popularSports', 
            'liveMatches',
            'promotions'
        ));
    }
    
    /**
     * Get featured matches for homepage
     */
    private function getFeaturedMatches()
    {
        // This would normally query the database
        return [
            [
                'id' => 1,
                'home_team' => 'Chelsea',
                'away_team' => 'Arsenal',
                'league' => 'Premier League',
                'kickoff' => now()->addHours(3),
                'home_odds' => 2.10,
                'draw_odds' => 3.40,
                'away_odds' => 3.20,
                'is_featured' => true
            ],
            [
                'id' => 2,
                'home_team' => 'LA Lakers',
                'away_team' => 'Boston Celtics',
                'league' => 'NBA',
                'kickoff' => now()->addHours(5),
                'home_odds' => 1.95,
                'away_odds' => 1.85,
                'is_featured' => true
            ],
            [
                'id' => 3,
                'home_team' => 'Real Madrid',
                'away_team' => 'Barcelona',
                'league' => 'La Liga',
                'kickoff' => now()->addDay(),
                'home_odds' => 2.50,
                'draw_odds' => 3.10,
                'away_odds' => 2.80,
                'is_featured' => true
            ]
        ];
    }
    
    /**
     * Get popular sports
     */
    private function getPopularSports()
    {
        return [
            ['name' => 'Football', 'icon' => 'bi-dribbble', 'matches' => 15],
            ['name' => 'Basketball', 'icon' => 'bi-basketball', 'matches' => 8],
            ['name' => 'Tennis', 'icon' => 'bi-tennis', 'matches' => 12],
            ['name' => 'Baseball', 'icon' => 'bi-baseball', 'matches' => 6],
            ['name' => 'Soccer', 'icon' => 'bi-soccer', 'matches' => 22],
            ['name' => 'Ice Hockey', 'icon' => 'bi-hockey-puck', 'matches' => 4]
        ];
    }
    
    /**
     * Get live matches
     */
    private function getLiveMatches()
    {
        return [
            [
                'id' => 101,
                'home_team' => 'Manchester United',
                'away_team' => 'Liverpool',
                'league' => 'Premier League',
                'minute' => 67,
                'home_score' => 1,
                'away_score' => 2,
                'status' => 'live'
            ],
            [
                'id' => 102,
                'home_team' => 'Warriors',
                'away_team' => 'Nets',
                'league' => 'NBA',
                'quarter' => 'Q3 8:42',
                'home_score' => 85,
                'away_score' => 78,
                'status' => 'live'
            ]
        ];
    }
    
    /**
     * Get active promotions
     */
    private function getActivePromotions()
    {
        return [
            [
                'title' => 'Welcome Bonus',
                'description' => '100% match up to $200',
                'code' => 'WELCOME100',
                'expires' => now()->addDays(30)
            ],
            [
                'title' => 'Free Bet Friday',
                'description' => 'Get $10 free bet every Friday',
                'code' => 'FRIDAY10',
                'expires' => now()->addDays(7)
            ]
        ];
    }
}
