<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_token',
        'device_type',
        'device_name',
        'ip_address',
        'user_agent',
        'location',
        'is_active',
        'last_activity',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeByDevice($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active && $this->expires_at > now();
    }

    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    public function expire(): void
    {
        $this->is_active = false;
        $this->save();
    }

    public function updateActivity(): void
    {
        $this->last_activity = now();
        $this->save();
    }

    public function extend($hours = 24): void
    {
        $this->expires_at = now()->addHours($hours);
        $this->save();
    }

    public function getDeviceIcon(): string
    {
        return match($this->device_type) {
            'mobile' => 'smartphone',
            'tablet' => 'tablet',
            'desktop' => 'monitor',
            default => 'device-unknown',
        };
    }

    public function getLocationDisplay(): string
    {
        return $this->location ?: 'Unknown Location';
    }

    public function getLastActivityForHumans(): string
    {
        return $this->last_activity->diffForHumans();
    }
}
