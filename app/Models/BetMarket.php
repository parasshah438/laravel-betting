<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetMarket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'description',
        'options_template',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options_template' => 'json',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function betOptions(): HasMany
    {
        return $this->hasMany(BetOption::class);
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

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getOptionsForMatch(Match $match)
    {
        return $this->betOptions()
            ->where('match_id', $match->id)
            ->active()
            ->get();
    }
}
