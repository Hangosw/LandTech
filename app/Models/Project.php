<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'slug',
        'label',
        'image',
        'district',
        'listing_count',
        'price_from',
        'tagline',
        'description',
        'highlights',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'highlights' => 'array',
        'listing_count' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Live listing count from searchable properties with matching project slug.
     */
    public function liveListingCount(): int
    {
        return Property::query()
            ->where('project', $this->slug)
            ->where('is_searchable', 1)
            ->count();
    }
}
