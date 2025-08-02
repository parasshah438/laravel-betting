<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'promotion_id',
        'bonus_amount',
        'wagering_requirement',
        'wagered_amount',
        'status',
        'claimed_at',
        'expires_at',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'bonus_amount' => 'decimal:8',
        'wagering_requirement' => 'decimal:8',
        'wagered_amount' => 'decimal:8',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'json',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
            ($this->expires_at && $this->expires_at->isPast());
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getRemainingWagering(): float
    {
        return max(0, $this->wagering_requirement - $this->wagered_amount);
    }

    public function getWageringProgress(): float
    {
        if ($this->wagering_requirement <= 0) {
            return 100;
        }

        return min(100, ($this->wagered_amount / $this->wagering_requirement) * 100);
    }

    public function addWagering($amount): void
    {
        $this->wagered_amount += $amount;
        
        if ($this->wagered_amount >= $this->wagering_requirement) {
            $this->status = 'completed';
            $this->completed_at = now();
        }
        
        $this->save();
    }

    public function expire(): void
    {
        $this->status = 'expired';
        $this->save();
    }

    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }
}
