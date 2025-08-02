<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OddsHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'bet_option_id',
        'odds',
        'recorded_at',
        'source',
    ];

    protected $casts = [
        'odds' => 'decimal:4',
        'recorded_at' => 'datetime',
    ];

    // Relationships
    public function betOption(): BelongsTo
    {
        return $this->belongsTo(BetOption::class);
    }

    // Scopes
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    // Helper methods
    public function getMovement($previousOdds): string
    {
        if ($this->odds > $previousOdds) {
            return 'up';
        } elseif ($this->odds < $previousOdds) {
            return 'down';
        }
        
        return 'stable';
    }

    public function getFormattedOdds(): string
    {
        return number_format($this->odds, 2);
    }
}
