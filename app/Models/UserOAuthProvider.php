<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOAuthProvider extends Model
{
    protected $table = 'user_oauth_providers';

    public $timestamps = false; // Only created_at is handled by default database timestamp

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_email',
        'access_token',
        'refresh_token',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * Get the user associated with this OAuth connection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
