<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyMedia extends Model
{
    public $timestamps = false; // Only created_at is handled by database default

    protected $fillable = [
        'property_id',
        'media_type',
        'file_url',
        'display_order',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Get the property that owns this image.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
