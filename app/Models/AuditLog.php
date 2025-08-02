<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'metadata' => 'json',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->morphTo('model', 'model_type', 'model_id');
        }
        return null;
    }

    // Scopes
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getChangedAttributes(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changed = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changed[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changed;
    }

    public function hasChanges(): bool
    {
        return !empty($this->getChangedAttributes());
    }

    public function getActionIcon(): string
    {
        return match($this->action) {
            'login' => 'log-in',
            'logout' => 'log-out',
            'created' => 'plus',
            'updated' => 'edit',
            'deleted' => 'trash',
            'bet_placed' => 'target',
            'deposit' => 'plus-circle',
            'withdrawal' => 'minus-circle',
            'promotion_claimed' => 'gift',
            default => 'activity',
        };
    }

    public function getActionColor(): string
    {
        return match($this->action) {
            'login' => 'success',
            'logout' => 'info',
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'bet_placed' => 'primary',
            'deposit' => 'success',
            'withdrawal' => 'info',
            'promotion_claimed' => 'warning',
            default => 'secondary',
        };
    }

    public function getUserName(): string
    {
        return $this->user ? $this->user->name : 'System';
    }

    public function getModelName(): string
    {
        if (!$this->model_type) {
            return 'Unknown';
        }

        return class_basename($this->model_type);
    }
}
