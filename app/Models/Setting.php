<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    // Helper methods
    public function getValue()
    {
        return match($this->type) {
            'json' => json_decode($this->value, true),
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'decimal' => (float) $this->value,
            default => $this->value,
        };
    }

    public function setValue($value): void
    {
        $this->value = match($this->type) {
            'json' => json_encode($value),
            'boolean' => $value ? 'true' : 'false',
            default => (string) $value,  
        };
        
        $this->save();
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    // Static helper methods
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->getValue();
    }

    public static function set($key, $value, $type = 'string', $group = 'general', $description = null, $isPublic = false): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        
        $setting->type = $type;
        $setting->group = $group;
        $setting->description = $description;
        $setting->is_public = $isPublic;
        $setting->setValue($value);
    }

    public static function getPublicSettings(): array
    {
        return static::public()
            ->get()
            ->pluck('value', 'key')
            ->map(function ($value, $key) {
                $setting = static::where('key', $key)->first();
                return $setting ? $setting->getValue() : $value;
            })
            ->toArray();
    }

    public static function getByGroup($group): array
    {
        return static::byGroup($group)
            ->get()
            ->pluck('value', 'key')
            ->map(function ($value, $key) {
                $setting = static::where('key', $key)->first();
                return $setting ? $setting->getValue() : $value;
            })
            ->toArray();
    }
}
