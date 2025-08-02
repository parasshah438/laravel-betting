<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Match extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'league_id',
        'home_team_id',
        'away_team_id',
        'match_name',
        'start_time',
        'status',
        'score',
        'live_data',
        'statistics',
        'venue',
        'referee',
        'notes',
        'is_featured',
        'live_betting_enabled',
        'external_id',
        'metadata',
        'finished_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'score' => 'json',
        'live_data' => 'json',
        'statistics' => 'json',
        'is_featured' => 'boolean',
        'live_betting_enabled' => 'boolean',
        'metadata' => 'json',
        'finished_at' => 'datetime',
    ];

    // Relationships
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function betOptions(): HasMany
    {
        return $this->hasMany(BetOption::class);
    }

    public function liveEvents(): HasMany
    {
        return $this->hasMany(LiveEvent::class);
    }

    public function betSelections(): HasMany
    {
        return $this->hasMany(BetSelection::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled']);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeLive($query)
    {
        return $query->whereIn('status', ['live', 'halftime']);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'finished');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
            ->where('status', 'scheduled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    // Helper methods
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isLive(): bool
    {
        return in_array($this->status, ['live', 'halftime']);
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPostponed(): bool
    {
        return $this->status === 'postponed';
    }

    public function getMatchDisplayName(): string
    {
        return $this->homeTeam->getDisplayName() . ' vs ' . $this->awayTeam->getDisplayName();
    }

    public function getHomeScore(): ?int
    {
        return $this->score['home'] ?? null;
    }

    public function getAwayScore(): ?int
    {
        return $this->score['away'] ?? null;
    }

    public function getScoreDisplay(): string
    {
        if (!$this->score) {
            return '-';
        }
        return ($this->getHomeScore() ?? 0) . ' - ' . ($this->getAwayScore() ?? 0);
    }

    public function getWinner(): ?string
    {
        if (!$this->isFinished() || !$this->score) {
            return null;
        }

        $homeScore = $this->getHomeScore();
        $awayScore = $this->getAwayScore();

        if ($homeScore > $awayScore) {
            return 'home';
        } elseif ($awayScore > $homeScore) {
            return 'away';
        }

        return 'draw';
    }

    public function canPlaceBets(): bool
    {
        return $this->isScheduled() || ($this->isLive() && $this->live_betting_enabled);
    }
}
