<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BettingPlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Sports
        $sports = [
            ['name' => 'Football', 'slug' => 'football', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Basketball', 'slug' => 'basketball', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Tennis', 'slug' => 'tennis', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Cricket', 'slug' => 'cricket', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Baseball', 'slug' => 'baseball', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Ice Hockey', 'slug' => 'ice-hockey', 'is_active' => true, 'sort_order' => 6],
            ['name' => 'Esports', 'slug' => 'esports', 'is_active' => true, 'sort_order' => 7],
        ];

        foreach ($sports as $sport) {
            DB::table('sports')->insert(array_merge($sport, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed Bet Markets
        $betMarkets = [
            ['name' => 'Match Winner', 'key' => 'match_winner', 'description' => 'Pick the winner of the match', 'sort_order' => 1],
            ['name' => 'Over/Under Goals', 'key' => 'over_under_goals', 'description' => 'Total goals over or under a specific number', 'sort_order' => 2],
            ['name' => 'Both Teams to Score', 'key' => 'both_teams_score', 'description' => 'Will both teams score in the match', 'sort_order' => 3],
            ['name' => 'Handicap', 'key' => 'handicap', 'description' => 'Betting with handicap advantage', 'sort_order' => 4],
            ['name' => 'Correct Score', 'key' => 'correct_score', 'description' => 'Predict the exact final score', 'sort_order' => 5],
            ['name' => 'Half Time/Full Time', 'key' => 'ht_ft', 'description' => 'Result at half time and full time', 'sort_order' => 6],
            ['name' => 'First Goal Scorer', 'key' => 'first_goal_scorer', 'description' => 'Who will score the first goal', 'sort_order' => 7],
            ['name' => 'Total Points', 'key' => 'total_points', 'description' => 'Total points scored in the game', 'sort_order' => 8],
            ['name' => 'Spread', 'key' => 'spread', 'description' => 'Point spread betting', 'sort_order' => 9],
            ['name' => 'Moneyline', 'key' => 'moneyline', 'description' => 'Straight win betting', 'sort_order' => 10],
        ];

        foreach ($betMarkets as $market) {
            DB::table('bet_markets')->insert(array_merge($market, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed Settings
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'BetPlatform', 'type' => 'string', 'group' => 'general', 'description' => 'Site name', 'is_public' => true],
            ['key' => 'site_description', 'value' => 'Online Betting Platform', 'type' => 'string', 'group' => 'general', 'description' => 'Site description', 'is_public' => true],
            ['key' => 'default_currency', 'value' => 'USD', 'type' => 'string', 'group' => 'general', 'description' => 'Default currency', 'is_public' => true],
            ['key' => 'supported_currencies', 'value' => '["USD","EUR","GBP","BTC","ETH"]', 'type' => 'json', 'group' => 'general', 'description' => 'Supported currencies', 'is_public' => true],
            
            // Betting Settings
            ['key' => 'min_bet_amount', 'value' => '1.00', 'type' => 'decimal', 'group' => 'betting', 'description' => 'Minimum bet amount', 'is_public' => true],
            ['key' => 'max_bet_amount', 'value' => '10000.00', 'type' => 'decimal', 'group' => 'betting', 'description' => 'Maximum bet amount', 'is_public' => true],
            ['key' => 'max_win_amount', 'value' => '100000.00', 'type' => 'decimal', 'group' => 'betting', 'description' => 'Maximum win amount', 'is_public' => true],
            ['key' => 'live_betting_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'betting', 'description' => 'Enable live betting', 'is_public' => true],
            ['key' => 'cashout_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'betting', 'description' => 'Enable cash out feature', 'is_public' => true],
            
            // Payment Settings
            ['key' => 'min_deposit_amount', 'value' => '10.00', 'type' => 'decimal', 'group' => 'payment', 'description' => 'Minimum deposit amount', 'is_public' => true],
            ['key' => 'max_deposit_amount', 'value' => '50000.00', 'type' => 'decimal', 'group' => 'payment', 'description' => 'Maximum deposit amount', 'is_public' => true],
            ['key' => 'min_withdrawal_amount', 'value' => '20.00', 'type' => 'decimal', 'group' => 'payment', 'description' => 'Minimum withdrawal amount', 'is_public' => true],
            ['key' => 'max_withdrawal_amount', 'value' => '25000.00', 'type' => 'decimal', 'group' => 'payment', 'description' => 'Maximum withdrawal amount', 'is_public' => true],
            ['key' => 'withdrawal_processing_time', 'value' => '24', 'type' => 'integer', 'group' => 'payment', 'description' => 'Withdrawal processing time in hours', 'is_public' => true],
            
            // Security Settings
            ['key' => 'two_factor_required', 'value' => 'false', 'type' => 'boolean', 'group' => 'security', 'description' => 'Require 2FA for all users', 'is_public' => false],
            ['key' => 'kyc_required', 'value' => 'true', 'type' => 'boolean', 'group' => 'security', 'description' => 'Require KYC verification', 'is_public' => false],
            ['key' => 'session_timeout', 'value' => '3600', 'type' => 'integer', 'group' => 'security', 'description' => 'Session timeout in seconds', 'is_public' => false],
            
            // Bonus Settings
            ['key' => 'welcome_bonus_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'bonus', 'description' => 'Enable welcome bonus', 'is_public' => true],
            ['key' => 'welcome_bonus_amount', 'value' => '100.00', 'type' => 'decimal', 'group' => 'bonus', 'description' => 'Welcome bonus amount', 'is_public' => true],
            ['key' => 'welcome_bonus_percentage', 'value' => '100', 'type' => 'decimal', 'group' => 'bonus', 'description' => 'Welcome bonus percentage', 'is_public' => true],
            ['key' => 'bonus_wagering_requirement', 'value' => '30', 'type' => 'decimal', 'group' => 'bonus', 'description' => 'Bonus wagering requirement multiplier', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create Admin User
        $adminUserId = DB::table('users')->insertGetId([
            'name' => 'Admin User',
            'email' => 'admin@betplatform.com',
            'email_verified_at' => now(),
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'is_verified' => true,
            'country' => 'US',
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Admin Profile
        DB::table('user_profiles')->insert([
            'user_id' => $adminUserId,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Admin Wallet
        DB::table('wallets')->insert([
            'user_id' => $adminUserId,
            'currency' => 'USD',
            'balance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "Betting platform database seeded successfully!\n";
    }
}
