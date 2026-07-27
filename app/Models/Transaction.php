<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public $timestamps = false; // Only created_at is handled by database default

    protected $fillable = [
        'property_id',
        'buyer_id',
        'seller_id',
        'transaction_date',
        'transaction_price',
        'status',
    ];

    protected $casts = [
        'property_id' => 'integer',
        'buyer_id' => 'integer',
        'seller_id' => 'integer',
        'transaction_date' => 'date',
        'transaction_price' => 'integer',
    ];

    /**
     * Get the property involved in this transaction.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who is the buyer in this transaction.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the user who is the seller in this transaction.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
