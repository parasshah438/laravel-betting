<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'channel',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'json',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function isRead(): bool
    {
        return $this->is_read;
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = now();
            $this->save();
        }
    }

    public function markAsUnread(): void
    {
        if ($this->is_read) {
            $this->is_read = false;
            $this->read_at = null;
            $this->save();
        }
    }

    public function getIcon(): string
    {
        return match($this->type) {
            'bet_won' => 'trophy',
            'bet_lost' => 'x-circle',
            'deposit_success' => 'plus-circle',
            'withdrawal_success' => 'minus-circle',
            'promotion' => 'gift',
            'security' => 'shield',
            'system' => 'info',
            default => 'bell',
        };
    }

    public function getColor(): string
    {
        return match($this->type) {
            'bet_won' => 'success',
            'bet_lost' => 'danger',
            'deposit_success' => 'success',
            'withdrawal_success' => 'info',
            'promotion' => 'warning',
            'security' => 'danger',
            'system' => 'info',
            default => 'primary',
        };
    }
}
