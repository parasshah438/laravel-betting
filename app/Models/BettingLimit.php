<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BettingLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'limit_type',
        'limit_category',
        'limit_amount',
        'used_amount',
        'period_start',
        'period_end',
        'is_active',
        'is_self_imposed',
        'notes',
    ];

    protected $casts = [
        'limit_amount' => 'decimal:8',
        'used_amount' => 'decimal:8',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'is_active' => 'boolean',
        'is_self_imposed' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('period_start', '<=', now())
            ->where('period_end', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('limit_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('limit_category', $category);
    }

    public function scopeSelfImposed($query)
    {
        return $query->where('is_self_imposed', true);
    }

    public function scopeAdminImposed($query)
    {
        return $query->where('is_self_imposed', false);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active 
            && $this->period_start <= now() 
            && $this->period_end >= now();
    }

    public function isExceeded(): bool
    {
        return $this->used_amount >= $this->limit_amount;
    }

    public function getRemainingAmount(): float
    {
        return max(0, $this->limit_amount - $this->used_amount);
    }

    public function getUsagePercentage(): float
    {
        if ($this->limit_amount <= 0) {
            return 0;
        }

        return min(100, ($this->used_amount / $this->limit_amount) * 100);
    }

    public function canSpend($amount): bool
    {
        if (!$this->isActive()) {
            return true;
        }

        return ($this->used_amount + $amount) <= $this->limit_amount;
    }

    public function addUsage($amount): void
    {
        $this->used_amount += $amount;
        $this->save();
    }

    public function resetUsage(): void
    {
        $this->used_amount = 0;
        $this->save();
    }

    public function extendPeriod(): void
    {
        $currentPeriod = $this->period_end->diffInDays($this->period_start);
        
        $this->period_start = $this->period_end;
        $this->period_end = $this->period_start->copy()->addDays($currentPeriod);
        $this->used_amount = 0;
        
        $this->save();
    }

    public function getDaysUntilReset(): int
    {
        return max(0, now()->diffInDays($this->period_end, false));
    }

    public function getHoursUntilReset(): int
    {
        return max(0, now()->diffInHours($this->period_end, false));
    }
}
