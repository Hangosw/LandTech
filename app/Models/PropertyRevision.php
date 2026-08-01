<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyRevision extends Model
{
    protected $fillable = [
        'property_id',
        'user_id',
        'action',
        'changes',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the property that this revision belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who made the edit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to get user display name.
     */
    public function getUserNameAttribute(): string
    {
        return $this->user->name ?? ($this->user_id ? 'Tài khoản #' . $this->user_id : 'Hệ thống');
    }
}
