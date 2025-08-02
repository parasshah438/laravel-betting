<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bet_id',
        'bet_type',
        'stake',
        'potential_win',
        'total_odds',
        'status',
        'payout',
        'currency',
        'is_live_bet',
        'is_system_bet',
        'system_config',
        'cashout_value',
        'cashout_available',
        'notes',
        'settled_at',
    ];

    protected $casts = [
        'stake' => 'decimal:8',
        'potential_win' => 'decimal:8',
        'total_odds' => 'decimal:4',
        'payout' => 'decimal:8',
        'is_live_bet' => 'boolean',
        'is_system_bet' => 'boolean',
        'system_config' => 'json',
        'cashout_value' => 'decimal:8',
        'cashout_available' => 'boolean',
        'settled_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function selections(): HasMany
    {
        return $this->hasMany(BetSelection::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'related_bet_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWon($query)
    {
        return $query->where('status', 'won');
    }

    public function scopeLost($query)
    {
        return $query->where('status', 'lost');
    }

    public function scopeVoid($query)
    {
        return $query->where('status', 'void');
    }

    public function scopeCashout($query)
    {
        return $query->where('status', 'cashout');
    }

    public function scopeLiveBets($query)
    {
        return $query->where('is_live_bet', true);
    }

    public function scopeSystemBets($query)
    {
        return $query->where('is_system_bet', true);
    }

    public function scopeBetType($query, $type)
    {
        return $query->where('bet_type', $type);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isWon(): bool
    {
        return $this->status === 'won';
    }

    public function isLost(): bool
    {
        return $this->status === 'lost';
    }

    public function isVoid(): bool
    {
        return $this->status === 'void';
    }

    public function isCashout(): bool
    {
        return $this->status === 'cashout';
    }

    public function isSettled(): bool
    {
        return in_array($this->status, ['won', 'lost', 'void', 'cashout']);
    }

    public function canCashout(): bool
    {
        return $this->isPending() && $this->cashout_available && $this->cashout_value > 0;
    }

    public function isSingle(): bool
    {
        return $this->bet_type === 'single';
    }

    public function isMultiple(): bool
    {
        return $this->bet_type === 'multiple';
    }

    public function isSystem(): bool
    {
        return $this->bet_type === 'system' || $this->is_system_bet;
    }

    public function calculateTotalOdds(): float
    {
        if ($this->isSingle()) {
            return $this->selections->first()->odds ?? 1.0;
        }

        return $this->selections->reduce(function ($carry, $selection) {
            return $carry * $selection->odds;
        }, 1.0);
    }

    public function calculatePotentialWin(): float
    {
        return $this->stake * $this->total_odds;
    }

    public function updateCashoutValue(): void
    {
        // This would be implemented based on your cashout calculation logic
        // For now, just a placeholder
        if ($this->isPending()) {
            $this->cashout_value = $this->stake * 0.8; // Example: 80% of stake
            $this->cashout_available = true;
            $this->save();
        }
    }

    public function settle($status, $payout = null): void
    {
        $this->status = $status;
        $this->settled_at = now();
        
        if ($payout !== null) {
            $this->payout = $payout;
        } elseif ($status === 'won') {
            $this->payout = $this->potential_win;
        } elseif ($status === 'void') {
            $this->payout = $this->stake; // Refund stake
        }

        $this->save();
    }

    public function getSelectionCount(): int
    {
        return $this->selections->count();
    }

    public function getWonSelections(): int
    {
        return $this->selections->where('status', 'won')->count();
    }

    public function getLostSelections(): int
    {
        return $this->selections->where('status', 'lost')->count();
    }

    public function getVoidSelections(): int
    {
        return $this->selections->where('status', 'void')->count();
    }
}
