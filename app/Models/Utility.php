<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'icon_name',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the properties that have this utility.
     */
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_utilities');
    }
}
