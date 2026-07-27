<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public $timestamps = false; // Custom created_at and confirmed_at fields

    protected $fillable = [
        'property_id',
        'renter_phone',
        'renter_name',
        'renter_email',
        'scheduled_date',
        'scheduled_time',
        'status',
        'notes',
        'confirmed_at',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'scheduled_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Get the property booked for viewing.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
