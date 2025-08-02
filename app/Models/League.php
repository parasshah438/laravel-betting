<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'name',
        'slug',
        'country',
        'logo',
        'description',
        'is_active',
        'sort_order',
        'settings',
        'external_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'json',
    ];

    // Relationships
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Match::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
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

    public function getUpcomingMatches()
    {
        return $this->matches()
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->get();
    }

    public function getLiveMatches()
    {
        return $this->matches()
            ->whereIn('status', ['live', 'halftime'])
            ->get();
    }
}
