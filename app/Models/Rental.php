<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    public $timestamps = false; // Only created_at is handled by database default

    protected $fillable = [
        'property_id',
        'renter_id',
        'owner_id',
        'start_date',
        'end_date',
        'monthly_price',
        'duration_months',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'renter_id' => 'integer',
        'owner_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_price' => 'integer',
        'duration_months' => 'integer',
    ];

    /**
     * Get the property leased in this rental contract.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who is the tenant/renter.
     */
    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    /**
     * Get the user who is the property owner/landlord.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
