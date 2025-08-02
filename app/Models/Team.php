<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'name',
        'short_name',
        'slug',
        'country',
        'logo',
        'description',
        'is_active',
        'stats',
        'external_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stats' => 'json',
    ];

    // Relationships
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(Match::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(Match::class, 'away_team_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getAllMatches()
    {
        return Match::where('home_team_id', $this->id)
            ->orWhere('away_team_id', $this->id)
            ->get();
    }

    public function getUpcomingMatches()
    {
        return Match::where(function ($query) {
                $query->where('home_team_id', $this->id)
                    ->orWhere('away_team_id', $this->id);
            })
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->get();
    }

    public function getRecentMatches($limit = 5)
    {
        return Match::where(function ($query) {
                $query->where('home_team_id', $this->id)
                    ->orWhere('away_team_id', $this->id);
            })
            ->where('status', 'finished')
            ->orderBy('finished_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getDisplayName(): string
    {
        return $this->short_name ?: $this->name;
    }
}
