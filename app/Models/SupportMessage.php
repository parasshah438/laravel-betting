<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachments',
        'is_staff_reply',
    ];

    protected $casts = [
        'attachments' => 'json',
        'is_staff_reply' => 'boolean',
    ];

    // Relationships
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeStaffReplies($query)
    {
        return $query->where('is_staff_reply', true);
    }

    public function scopeUserMessages($query)
    {
        return $query->where('is_staff_reply', false);
    }

    // Helper methods
    public function isStaffReply(): bool
    {
        return $this->is_staff_reply;
    }

    public function isUserMessage(): bool
    {
        return !$this->is_staff_reply;
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function getAttachmentCount(): int
    {
        return count($this->attachments ?? []);
    }
}
