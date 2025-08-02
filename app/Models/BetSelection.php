<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'bet_id',
        'match_id',
        'bet_option_id',
        'selection_name',
        'odds',
        'status',
        'match_info',
        'result_info',
    ];

    protected $casts = [
        'odds' => 'decimal:4',
        'match_info' => 'json',
        'result_info' => 'json',
    ];

    // Relationships
    public function bet(): BelongsTo
    {
        return $this->belongsTo(Bet::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function betOption(): BelongsTo
    {
        return $this->belongsTo(BetOption::class);
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

    public function isSettled(): bool
    {
        return in_array($this->status, ['won', 'lost', 'void']);
    }

    public function settle($status, $resultInfo = null): void
    {
        $this->status = $status;
        
        if ($resultInfo) {
            $this->result_info = $resultInfo;
        }

        $this->save();
    }

    public function getMatchName(): string
    {
        return $this->match_info['match_name'] ?? $this->match->getMatchDisplayName();
    }

    public function getLeagueName(): string
    {
        return $this->match_info['league_name'] ?? $this->match->league->name;
    }

    public function getSportName(): string
    {
        return $this->match_info['sport_name'] ?? $this->match->sport->name;
    }
}
