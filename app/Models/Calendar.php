<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calendar extends Model
{
    protected $table = 'calendar'; // Map to calendar table

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'date',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'date' => 'date',
    ];

    /**
     * Get the property associated with this calendar slot.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
