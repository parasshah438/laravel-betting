<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_number',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'assigned_at',
        'resolved_at',
        'attachments',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'attachments' => 'json',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    // Helper methods
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }

    public function assignTo(User $agent): void
    {
        $this->assigned_to = $agent->id;
        $this->assigned_at = now();
        $this->status = 'in_progress';
        $this->save();
    }

    public function resolve(): void
    {
        $this->status = 'resolved';
        $this->resolved_at = now();
        $this->save();
    }

    public function close(): void
    {
        $this->status = 'closed';
        $this->save();
    }

    public function reopen(): void
    {
        $this->status = 'open';
        $this->resolved_at = null;
        $this->save();
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'open' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'primary',
        };
    }

    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark',
            default => 'primary',
        };
    }

    public function getResponseTime(): ?int
    {
        $firstStaffReply = $this->messages()
            ->where('is_staff_reply', true)
            ->orderBy('created_at')
            ->first();

        if (!$firstStaffReply) {
            return null;
        }

        return $this->created_at->diffInMinutes($firstStaffReply->created_at);
    }

    public function getLastActivity(): ?string
    {
        $lastMessage = $this->messages()->latest()->first();
        
        if (!$lastMessage) {
            return $this->created_at->diffForHumans();
        }

        return $lastMessage->created_at->diffForHumans();
    }
}
