<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\UserPromotion;
use App\Models\Bonus;
use App\Models\UserBonus;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Bet;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    /**
     * Get available promotions
     */
    public function getPromotions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:welcome,deposit,cashback,loyalty,referral,tournament',
            'status' => 'nullable|string|in:active,inactive',
            'is_featured' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $query = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        $promotions = $query->get();

        // Filter promotions based on user's eligibility
        $availablePromotions = $promotions->filter(function ($promotion) use ($user) {
            return $promotion->isEligibleForUser($user);
        })->values();

        // Get user's active promotions
        $userPromotions = UserPromotion::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('promotion')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'available_promotions' => $availablePromotions,
                'user_promotions' => $userPromotions
            ]
        ]);
    }

    /**
     * Get specific promotion details
     */
    public function getPromotion(Request $request, $promotionId): JsonResponse
    {
        $promotion = Promotion::find($promotionId);

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion not found'
            ], 404);
        }

        if (!$promotion->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion is not active'
            ], 400);
        }

        $user = $request->user();

        // Check if user is eligible
        $isEligible = $promotion->isEligibleForUser($user);

        // Check if user has already claimed
        $userPromotion = UserPromotion::where('user_id', $user->id)
            ->where('promotion_id', $promotion->id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'promotion' => $promotion,
                'is_eligible' => $isEligible,
                'is_claimed' => $userPromotion ? true : false,
                'user_promotion' => $userPromotion
            ]
        ]);
    }

    /**
     * Claim a promotion
     */
    public function claimPromotion(Request $request, $promotionId): JsonResponse
    {
        $promotion = Promotion::find($promotionId);

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion not found'
            ], 404);
        }

        if (!$promotion->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion is not active'
            ], 400);
        }

        $user = $request->user();

        // Check if user is eligible
        if (!$promotion->isEligibleForUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not eligible for this promotion'
            ], 400);
        }

        // Check if user has already claimed
        $existingClaim = UserPromotion::where('user_id', $user->id)
            ->where('promotion_id', $promotion->id)
            ->first();

        if ($existingClaim) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion already claimed'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create user promotion record
            $userPromotion = UserPromotion::create([
                'user_id' => $user->id,
                'promotion_id' => $promotion->id,
                'claimed_at' => now(),
                'status' => 'active',
                'expires_at' => $promotion->duration_days ? 
                    now()->addDays($promotion->duration_days) : null,
                'wagering_requirement' => $promotion->wagering_requirement,
                'remaining_wagering' => $promotion->wagering_requirement,
            ]);

            // Process promotion based on type
            $this->processPromotionClaim($promotion, $user, $userPromotion);

            // Update promotion usage
            $promotion->increment('usage_count');

            // Log promotion claim
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'promotion_claimed',
                'model_type' => Promotion::class,
                'model_id' => $promotion->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'promotion_type' => $promotion->type,
                    'promotion_name' => $promotion->name,
                    'bonus_amount' => $promotion->bonus_amount,
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Promotion claimed successfully',
                'data' => [
                    'user_promotion' => $userPromotion->fresh()->load('promotion')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to claim promotion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user bonuses
     */
    public function getUserBonuses(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string|in:active,completed,expired,cancelled',
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

        $query = UserBonus::where('user_id', $user->id)
            ->with(['bonus', 'userPromotion.promotion'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bonuses = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'bonuses' => $bonuses->items(),
                'pagination' => [
                    'current_page' => $bonuses->currentPage(),
                    'last_page' => $bonuses->lastPage(),
                    'per_page' => $bonuses->perPage(),
                    'total' => $bonuses->total(),
                ]
            ]
        ]);
    }

    /**
     * Get bonus progress
     */
    public function getBonusProgress(Request $request, $bonusId): JsonResponse
    {
        $userBonus = UserBonus::where('id', $bonusId)
            ->where('user_id', $request->user()->id)
            ->with(['bonus', 'userPromotion.promotion'])
            ->first();

        if (!$userBonus) {
            return response()->json([
                'success' => false,
                'message' => 'Bonus not found'
            ], 404);
        }

        $progress = [
            'wagering_progress' => $userBonus->getWageringProgress(),
            'remaining_wagering' => $userBonus->remaining_wagering,
            'total_wagering_required' => $userBonus->wagering_requirement,
            'expires_at' => $userBonus->expires_at,
            'days_remaining' => $userBonus->expires_at ? 
                now()->diffInDays($userBonus->expires_at, false) : null,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'bonus' => $userBonus,
                'progress' => $progress
            ]
        ]);
    }

    /**
     * Apply promo code
     */
    public function applyPromoCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'promo_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $promoCode = strtoupper($request->promo_code);
        $user = $request->user();

        $promotion = Promotion::where('promo_code', $promoCode)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->first();

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired promo code'
            ], 400);
        }

        // Check if user is eligible
        if (!$promotion->isEligibleForUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not eligible for this promotion'
            ], 400);
        }

        // Check if user has already used this promo code
        $existingClaim = UserPromotion::where('user_id', $user->id)
            ->where('promotion_id', $promotion->id)
            ->first();

        if ($existingClaim) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code already used'
            ], 400);
        }

        // Auto-claim the promotion
        try {
            DB::beginTransaction();

            $userPromotion = UserPromotion::create([
                'user_id' => $user->id,
                'promotion_id' => $promotion->id,
                'claimed_at' => now(),
                'status' => 'active',
                'expires_at' => $promotion->duration_days ? 
                    now()->addDays($promotion->duration_days) : null,
                'wagering_requirement' => $promotion->wagering_requirement,
                'remaining_wagering' => $promotion->wagering_requirement,
            ]);

            // Process promotion
            $this->processPromotionClaim($promotion, $user, $userPromotion);

            // Update promotion usage
            $promotion->increment('usage_count');

            // Log promo code usage
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'promo_code_used',
                'model_type' => Promotion::class,
                'model_id' => $promotion->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => [
                    'promo_code' => $promoCode,
                    'promotion_name' => $promotion->name,
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Promo code applied successfully',
                'data' => [
                    'promotion' => $promotion,
                    'user_promotion' => $userPromotion->fresh()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply promo code',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process promotion claim based on type
     */
    private function processPromotionClaim(Promotion $promotion, $user, UserPromotion $userPromotion)
    {
        switch ($promotion->type) {
            case 'welcome':
            case 'deposit':
                $this->processDepositBonus($promotion, $user, $userPromotion);
                break;
            
            case 'cashback':
                $this->processCashbackBonus($promotion, $user, $userPromotion);
                break;
            
            case 'loyalty':
                $this->processLoyaltyBonus($promotion, $user, $userPromotion);
                break;
            
            case 'referral':
                $this->processReferralBonus($promotion, $user, $userPromotion);
                break;
            
            case 'tournament':
                $this->processTournamentEntry($promotion, $user, $userPromotion);
                break;
        }
    }

    /**
     * Process deposit bonus
     */
    private function processDepositBonus(Promotion $promotion, $user, UserPromotion $userPromotion)
    {
        // For deposit bonuses, we typically wait for the deposit to trigger the bonus
        // This creates a pending bonus that will be activated on deposit
        
        if ($promotion->bonus_amount > 0) {
            $bonus = Bonus::where('promotion_id', $promotion->id)->first();
            
            if ($bonus) {
                UserBonus::create([
                    'user_id' => $user->id,
                    'bonus_id' => $bonus->id,
                    'user_promotion_id' => $userPromotion->id,
                    'amount' => $promotion->bonus_amount,
                    'wagering_requirement' => $promotion->wagering_requirement,
                    'remaining_wagering' => $promotion->wagering_requirement,
                    'status' => 'pending', // Will be activated on deposit
                    'expires_at' => $promotion->duration_days ? 
                        now()->addDays($promotion->duration_days) : null,
                ]);
            }
        }
    }

    /**
     * Process cashback bonus
     */
    private function processCashbackBonus(Promotion $promotion, $user, UserPromotion $userPromotion)
    {
        // Calculate cashback based on recent losses
        $period = $promotion->settings['cashback_period'] ?? 7; // days
        $cashbackRate = $promotion->settings['cashback_rate'] ?? 0.1; // 10%
        
        $recentLosses = Bet::where('user_id', $user->id)
            ->where('status', 'lost')
            ->where('created_at', '>=', now()->subDays($period))
            ->sum('stake');
        
        $cashbackAmount = $recentLosses * $cashbackRate;
        $maxCashback = $promotion->settings['max_cashback'] ?? 1000;
        
        $cashbackAmount = min($cashbackAmount, $maxCashback);
        
        if ($cashbackAmount > 0) {
            // Add cashback to user's wallet
            $wallet = Wallet::where('user_id', $user->id)
                ->where('currency', $promotion->currency)
                ->first();
            
            if ($wallet) {
                $wallet->addBalance($cashbackAmount);
                $wallet->addBalance($cashbackAmount, 'bonus_balance');
                
                // Create transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'transaction_id' => 'CASHBACK_' . Str::random(12),
                    'type' => 'cashback_bonus',
                    'amount' => $cashbackAmount,
                    'balance_before' => $wallet->balance - $cashbackAmount,
                    'balance_after' => $wallet->balance,
                    'currency' => $promotion->currency,
                    'status' => 'completed',
                    'description' => "Cashback bonus - {$promotion->name}",
                    'related_promotion_id' => $promotion->id,
                    'processed_at' => now(),
                ]);
            }
        }
    }

    /**
     * Process loyalty bonus
     */
    private function processLoyaltyBonus(Promotion $promotion, $user, UserPromotion $userPromotion)
    {
        // Award loyalty points or bonus based on user's betting activity
        $bonusAmount = $promotion->bonus_amount;
        
        if ($bonusAmount > 0) {
            $wallet = Wallet::where('user_id', $user->id)
                ->where('currency', $promotion->currency)
                ->first();
            
            if ($wallet) {
                $wallet->addBalance($bonusAmount);
                $wallet->addBalance($bonusAmount, 'bonus_balance');
                
                Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'transaction_id' => 'LOYALTY_' . Str::random(12),
                    'type' => 'loyalty_bonus',
                    'amount' => $bonusAmount,
                    'balance_before' => $wallet->balance - $bonusAmount,
                    'balance_after' => $wallet->balance,
                    'currency' => $promotion->currency,
                    'status' => 'completed',
                    'description' => "Loyalty bonus - {$promotion->name}",
                    'related_promotion_id' => $promotion->id,
                    'processed_at' => now(),
                ]);
            }
        }
    }

    /**
     * Process referral bonus
     */
    private function processReferralBonus(Promotion $promotion, $user, UserPromotion $userPromotion)
    {
        // This would typically be triggered when a referred user meets certain criteria
        // For now, we'll create a pending bonus
        
        $bonusAmount = $promotion->bonus_amount;
        
        if ($bonusAmount > 0) {
            $bonus = Bonus::where('promotion_id', $promotion->id)->first();
            
            if ($bonus) {
                UserBonus::create([
                    'user_id' => $user->id,
                    'bonus_id' => $bonus->id,
                    'user_promotion_id' => $userPromotion->id,
                    'amount' => $bonusAmount,
                    'wagering_requirement' => $promotion->wagering_requirement,
                    'remaining_wagering' => $promotion->wagering_requirement,
                    'status' => 'active',
                    'expires_at' => $promotion->duration_days ? 
                        now()->addDays($promotion->duration_days) : null,
                ]);
            }
        }
    }

    /**
     * Process tournament entry
     */
    private function processTournamentEntry(Promotion $promotion, $user, UserPromotion $userPromotion)
    {
        // Tournament entry logic would go here
        // This might involve creating tournament participant records
        // or awarding tournament-specific bonuses
    }
}
