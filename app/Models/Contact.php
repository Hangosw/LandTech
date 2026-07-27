<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    public $timestamps = false; // Only created_at is handled by database default

    protected $fillable = [
        'property_id',
        'buyer_phone',
        'buyer_name',
        'buyer_email',
        'message',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
    ];

    /**
     * Get the property associated with this contact request.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
