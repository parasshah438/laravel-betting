<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AccountController extends Controller
{
    /**
     * Show the account dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $statistics = $this->getAccountStatistics();
        $recentTransactions = $this->getRecentTransactions();
        $activeBonuses = $this->getActiveBonuses();
        
        return view('betting.account', compact(
            'user',
            'statistics',
            'recentTransactions', 
            'activeBonuses'
        ));
    }
    
    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255'
        ]);
        
        $user = Auth::user();
        $user->update($request->only([
            'first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'address'
        ]));
        
        return redirect()->route('account')->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update account settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'default_stake' => 'required|numeric|min:1',
            'odds_format' => 'required|in:decimal,fractional,american',
            'default_market' => 'required|string',
            'accept_odds_changes' => 'boolean',
            'daily_deposit_limit' => 'nullable|numeric|min:0',
            'session_time_limit' => 'nullable|integer|min:0'
        ]);
        
        // Update user settings (you might want to create a settings table)
        $user = Auth::user();
        $settings = $user->settings ?? [];
        $settings = array_merge($settings, $request->all());
        $user->settings = $settings;
        $user->save();
        
        return redirect()->route('account')->with('success', 'Settings updated successfully!');
    }
    
    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('account')->with('success', 'Password updated successfully!');
    }
    
    /**
     * Process deposit
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:5000',
            'payment_method' => 'required|string',
        ]);
        
        // Process deposit logic here
        $user = Auth::user();
        
        // Simulate deposit processing
        $deposit = $this->processDeposit([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'completed'
        ]);
        
        if ($deposit['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Deposit processed successfully!',
                'new_balance' => $user->balance + $request->amount
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Deposit failed. Please try again.'
        ]);
    }
    
    /**
     * Process withdrawal
     */
    public function withdraw(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'amount' => 'required|numeric|min:20|max:' . $user->balance,
            'withdrawal_method' => 'required|string',
        ]);
        
        // Process withdrawal logic here
        $withdrawal = $this->processWithdrawal([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'withdrawal_method' => $request->withdrawal_method,
            'status' => 'pending'
        ]);
        
        if ($withdrawal['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully!',
                'new_balance' => $user->balance - $request->amount
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Withdrawal failed. Please try again.'
        ]);
    }
    
    /**
     * Get transaction history
     */
    public function transactions(Request $request)
    {
        $transactions = $this->getUserTransactions($request->all());
        
        if ($request->ajax()) {
            return response()->json($transactions);
        }
        
        return view('betting.transactions', compact('transactions'));
    }
    
    /**
     * Get user balance (API)
     */
    public function getBalance()
    {
        $user = Auth::user();
        
        return response()->json([
            'main_balance' => $user->balance ?? 1250.75,
            'bonus_balance' => $user->bonus_balance ?? 125.50,
            'pending_withdrawals' => 0.00
        ]);
    }
    
    /**
     * Get user notifications (API)
     */
    public function getNotifications()
    {
        $notifications = [
            [
                'id' => 1,
                'type' => 'bet_win',
                'title' => 'Bet Won!',
                'message' => 'Your bet on Arsenal vs Chelsea won $85.50',
                'created_at' => now()->subMinutes(30),
                'read' => false
            ],
            [
                'id' => 2,
                'type' => 'promotion',
                'title' => 'New Bonus Available',
                'message' => 'Weekend reload bonus: 50% up to $100',
                'created_at' => now()->subHours(2),
                'read' => false
            ]
        ];
        
        return response()->json($notifications);
    }
    
    /**
     * Get account statistics
     */
    private function getAccountStatistics()
    {
        return [
            'member_since' => Auth::user()->created_at ?? now()->subYear(),
            'total_bets' => 1247,
            'win_rate' => 68.4,
            'vip_level' => 'Gold'
        ];
    }
    
    /**
     * Get recent transactions
     */
    private function getRecentTransactions()
    {
        return [
            [
                'type' => 'deposit',
                'description' => 'Credit Card Deposit',
                'amount' => 100.00,
                'status' => 'completed',
                'date' => now()
            ],
            [
                'type' => 'bet_win',
                'description' => 'Chelsea vs Arsenal - Arsenal Win',
                'amount' => 85.50,
                'status' => 'completed',
                'date' => now()->subDays(1)
            ],
            [
                'type' => 'bet',
                'description' => 'Chelsea vs Arsenal - Arsenal Win',
                'amount' => -50.00,
                'status' => 'completed',
                'date' => now()->subDays(1)
            ]
        ];
    }
    
    /**
     * Get active bonuses
     */
    private function getActiveBonuses()
    {
        return [
            [
                'name' => 'Welcome Bonus',
                'amount' => 125.50,
                'wagering_required' => 2510.00,
                'progress' => 65,
                'expires' => now()->addDays(15)
            ],
            [
                'name' => 'Weekend Reload',
                'amount' => 50.00,
                'wagering_required' => 1000.00,
                'progress' => 30,
                'expires' => now()->addDays(3)
            ]
        ];
    }
    
    /**
     * Process deposit (simulate)
     */
    private function processDeposit($data)
    {
        // Simulate payment processing
        return [
            'success' => true,
            'transaction_id' => 'DEP-' . rand(100000, 999999)
        ];
    }
    
    /**
     * Process withdrawal (simulate)
     */
    private function processWithdrawal($data)
    {
        // Simulate withdrawal processing
        return [
            'success' => true,
            'transaction_id' => 'WITH-' . rand(100000, 999999)
        ];
    }
    
    /**
     * Get user transactions (simulate)
     */
    private function getUserTransactions($filters = [])
    {
        return [
            [
                'id' => 1,
                'type' => 'deposit',
                'description' => 'Credit Card Deposit',
                'amount' => 100.00,
                'status' => 'completed',
                'date' => now()
            ],
            [
                'id' => 2,
                'type' => 'bet_win',
                'description' => 'Arsenal Win',
                'amount' => 85.50,
                'status' => 'completed',
                'date' => now()->subDays(1)
            ]
        ];
    }
}
