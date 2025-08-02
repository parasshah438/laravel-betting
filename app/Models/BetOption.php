<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'bet_market_id',
        'name',
        'value',
        'odds',
        'opening_odds',
        'is_active',
        'is_suspended',
        'bet_count',
        'total_stake',
        'metadata',
    ];

    protected $casts = [
        'odds' => 'decimal:4',
        'opening_odds' => 'decimal:4',
        'is_active' => 'boolean',
        'is_suspended' => 'boolean',
        'total_stake' => 'decimal:8',
        'metadata' => 'json',
    ];

    // Relationships
    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function betMarket(): BelongsTo
    {
        return $this->belongsTo(BetMarket::class);
    }

    public function betSelections(): HasMany
    {
        return $this->hasMany(BetSelection::class);
    }

    public function oddsHistory(): HasMany
    {
        return $this->hasMany(OddsHistory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('is_suspended', false);
    }

    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }

    public function scopeByMarket($query, $marketId)
    {
        return $query->where('bet_market_id', $marketId);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active && !$this->is_suspended;
    }

    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    public function canBet(): bool
    {
        return $this->isActive() && $this->match->canPlaceBets();
    }

    public function updateOdds($newOdds): void
    {
        // Store odds history
        $this->oddsHistory()->create([
            'odds' => $this->odds,
            'recorded_at' => now(),
        ]);

        // Update current odds
        $this->odds = $newOdds;
        $this->save();
    }

    public function incrementBetCount($stake = 0): void
    {
        $this->bet_count++;
        $this->total_stake += $stake;
        $this->save();
    }

    public function getOddsMovement(): string
    {
        if (!$this->opening_odds) {
            return 'stable';
        }

        if ($this->odds > $this->opening_odds) {
            return 'up';
        } elseif ($this->odds < $this->opening_odds) {
            return 'down';
        }

        return 'stable';
    }

    public function getFormattedOdds(): string
    {
        return number_format($this->odds, 2);
    }
}
