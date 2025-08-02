<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'balance',
        'bonus_balance',
        'locked_balance',
        'withdrawable_balance',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'bonus_balance' => 'decimal:8',
        'locked_balance' => 'decimal:8',
        'withdrawable_balance' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    // Helper methods
    public function getTotalBalanceAttribute(): string
    {
        return $this->balance + $this->bonus_balance;
    }

    public function getAvailableBalanceAttribute(): string
    {
        return $this->balance - $this->locked_balance;
    }

    public function canWithdraw($amount): bool
    {
        return $this->withdrawable_balance >= $amount;
    }

    public function canBet($amount): bool
    {
        return $this->getAvailableBalanceAttribute() >= $amount;
    }

    public function addBalance($amount, $type = 'balance'): void
    {
        $this->{$type} += $amount;
        $this->save();
    }

    public function subtractBalance($amount, $type = 'balance'): void
    {
        $this->{$type} -= $amount;
        $this->save();
    }

    public function lockBalance($amount): void
    {
        $this->balance -= $amount;
        $this->locked_balance += $amount;
        $this->save();
    }

    public function unlockBalance($amount): void
    {
        $this->locked_balance -= $amount;
        $this->balance += $amount;
        $this->save();
    }
}
