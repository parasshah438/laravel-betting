<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'code',
        'type',
        'conditions',
        'bonus_amount',
        'bonus_percentage',
        'max_bonus',
        'min_deposit',
        'wagering_requirement',
        'usage_limit',
        'user_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
        'image',
        'target_users',
    ];

    protected $casts = [
        'conditions' => 'json',
        'bonus_amount' => 'decimal:8',
        'bonus_percentage' => 'decimal:2',
        'max_bonus' => 'decimal:8',
        'min_deposit' => 'decimal:8',
        'wagering_requirement' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'target_users' => 'json',
    ];

    // Relationships
    public function userPromotions(): HasMany
    {
        return $this->hasMany(UserPromotion::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereRaw('used_count < usage_limit');
            });
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active 
            && $this->start_date <= now() 
            && $this->end_date >= now();
    }

    public function isAvailable(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canUserClaim(User $user): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        // Check if user already claimed this promotion
        $userClaimedCount = $this->userPromotions()
            ->where('user_id', $user->id)
            ->count();

        if ($userClaimedCount >= $this->user_limit) {
            return false;
        }

        // Check target users criteria if exists
        if ($this->target_users) {
            // Implementation depends on your targeting criteria
            // Example: country, user level, etc.
        }

        return true;
    }

    public function calculateBonusAmount($depositAmount = null): float
    {
        if ($this->bonus_amount) {
            return min($this->bonus_amount, $this->max_bonus ?: PHP_FLOAT_MAX);
        }

        if ($this->bonus_percentage && $depositAmount) {
            $calculatedBonus = $depositAmount * ($this->bonus_percentage / 100);
            return min($calculatedBonus, $this->max_bonus ?: PHP_FLOAT_MAX);
        }

        return 0;
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    public function getRemainingUsage(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }

        return max(0, $this->usage_limit - $this->used_count);
    }
}
