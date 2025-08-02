<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'event_type',
        'minute',
        'team',
        'player',
        'description',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    // Relationships
    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeByTeam($query, $team)
    {
        return $query->where('team', $team);
    }

    public function scopeGoals($query)
    {
        return $query->where('event_type', 'goal');
    }

    public function scopeCards($query)
    {
        return $query->whereIn('event_type', ['yellow_card', 'red_card']);
    }

    public function scopeSubstitutions($query)
    {
        return $query->where('event_type', 'substitution');
    }

    // Helper methods
    public function isGoal(): bool
    {
        return $this->event_type === 'goal';
    }

    public function isCard(): bool
    {
        return in_array($this->event_type, ['yellow_card', 'red_card']);
    }

    public function isSubstitution(): bool
    {
        return $this->event_type === 'substitution';
    }

    public function getEventIcon(): string
    {
        return match($this->event_type) {
            'goal' => 'target',
            'yellow_card' => 'square',
            'red_card' => 'square',
            'substitution' => 'refresh-cw',
            'offside' => 'flag',
            'corner' => 'corner-down-right',
            'free_kick' => 'circle',
            default => 'activity',
        };
    }

    public function getEventColor(): string
    {
        return match($this->event_type) {
            'goal' => 'success',
            'yellow_card' => 'warning',
            'red_card' => 'danger',
            'substitution' => 'info',
            'offside' => 'secondary',
            'corner' => 'primary',
            'free_kick' => 'info',
            default => 'secondary',
        };
    }

    public function getMinuteDisplay(): string
    {
        if (!$this->minute) {
            return '-';
        }

        return $this->minute . "'";
    }

    public function getTeamDisplay(): string
    {
        return match($this->team) {
            'home' => $this->match->homeTeam->getDisplayName(),
            'away' => $this->match->awayTeam->getDisplayName(),
            default => 'Unknown',
        };
    }
}
