<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'cover_image_url',
        'property_type',
        'project',
        'address',
        'district',
        'unit_number',
        'lat',
        'lng',
        'bedrooms',
        'bathrooms',
        'area',
        'price',
        'price_per_sqm',
        'monthly_price',
        'min_rent_period',
        'transaction_type',
        'distance_to_beach',
        'status',
        'is_searchable',
        'view_count',
        'contact_count',
        'active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'area' => 'float',
        'price' => 'integer',
        'price_per_sqm' => 'integer',
        'monthly_price' => 'integer',
        'min_rent_period' => 'integer',
        'distance_to_beach' => 'integer',
        'view_count' => 'integer',
        'contact_count' => 'integer',
        'is_searchable' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where('active', 1);
        });

        // Automatically set is_searchable ONLY when status is 'sansangchothue'
        static::saving(function ($property) {
            $property->is_searchable = ($property->status === 'sansangchothue');
        });

        // Auto-record revision log on property updates
        static::updated(function ($property) {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('property_revisions')) {
                    return;
                }

                $ignoredFields = ['updated_at', 'created_at', 'view_count', 'contact_count'];
                $changes = array_diff_key($property->getChanges(), array_flip($ignoredFields));

                if (empty($changes)) {
                    return;
                }

                $original = $property->getOriginal();
                $oldValues = [];
                $newValues = [];
                $changeSummary = [];

                foreach ($changes as $field => $newValue) {
                    $oldValue = $original[$field] ?? null;
                    $oldValues[$field] = $oldValue;
                    $newValues[$field] = $newValue;
                    $changeSummary[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }

                $sessionUser = session('user');
                $userId = $sessionUser ? ($sessionUser->id ?? $sessionUser['id'] ?? null) : auth()->id();

                $action = array_key_exists('status', $changes) ? 'status_changed' : 'updated';
                if (array_key_exists('active', $changes) && $changes['active'] == 0) {
                    $action = 'soft_deleted';
                }

                PropertyRevision::create([
                    'property_id' => $property->id,
                    'user_id' => $userId,
                    'action' => $action,
                    'changes' => $changeSummary,
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Throwable $e) {
                // Ignore failure gracefully
            }
        });

        static::created(function ($property) {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('property_revisions')) {
                    return;
                }

                $sessionUser = session('user');
                $userId = $sessionUser ? ($sessionUser->id ?? $sessionUser['id'] ?? null) : auth()->id();

                $attributes = array_diff_key($property->getAttributes(), array_flip(['updated_at', 'created_at']));

                PropertyRevision::create([
                    'property_id' => $property->id,
                    'user_id' => $userId,
                    'action' => 'created',
                    'changes' => null,
                    'old_values' => null,
                    'new_values' => $attributes,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Throwable $e) {
                // Ignore failure gracefully
            }
        });
    }

    /**
     * Get revision history logs for this property.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(PropertyRevision::class)->latest();
    }

    /**
     * Get the user who owns/posted this property.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the media for the property.
     */
    public function media(): HasMany
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyMedia::class)->where('media_type', 'image')->orderBy('display_order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(PropertyMedia::class)->where('media_type', 'video')->orderBy('display_order');
    }

    /**
     * Get the buyer contacts for this property.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Get the bookings for this property.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the calendar entries for this property.
     */
    public function calendar(): HasMany
    {
        return $this->hasMany(Calendar::class);
    }

    /**
     * Get transactions history of this property.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get rental contracts of this property.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Get the utilities for the property.
     */
    public function utilities()
    {
        return $this->belongsToMany(Utility::class, 'property_utilities');
    }

    /**
     * Get the label for the property type in Vietnamese.
     */
    public function getTypeLabelAttribute()
    {
        $typeLabels = [
            'apartment' => 'Căn hộ',
            'house' => 'Nhà phố',
            'villa' => 'Biệt thự',
            'office' => 'Văn phòng',
            'commercial' => 'Mặt bằng kinh doanh',
            'land' => 'Đất nền',
            'shophouse' => 'Shophouse',
        ];

        return $typeLabels[$this->property_type] ?? ucfirst($this->property_type);
    }

    /**
     * Get the label for the status in Vietnamese.
     */
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'nhap' => 'Nháp',
            'choduyet' => 'Chờ duyệt',
            'sansangchothue' => 'Sẵn sàng cho thuê',
            'dachothue' => 'Đã cho thuê',
            'taman' => 'Tạm ẩn',
            'hethantin' => 'Hết hạn tin',
            'ngungkhaithac' => 'Ngừng khai thác',
            'bigovipham' => 'Bị gỡ (vi phạm)',
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Neighbor listings for Back / Next navigation (ordered by id asc).
     * Wraps around when at either end so both buttons stay useful.
     *
     * @return array{prev: ?self, next: ?self}
     */
    public function detailNeighbors(bool $publicOnly = true): array
    {
        $base = static::query()->select(['id', 'slug', 'title']);

        if ($publicOnly) {
            $base->where('is_searchable', 1);
        } else {
            $base->withoutGlobalScope('active');
        }

        $prev = (clone $base)
            ->where('id', '<', $this->id)
            ->orderByDesc('id')
            ->first();

        $next = (clone $base)
            ->where('id', '>', $this->id)
            ->orderBy('id')
            ->first();

        // Wrap: last → first, first → last (only when other listings exist)
        if (! $prev) {
            $prev = (clone $base)
                ->where('id', '!=', $this->id)
                ->orderByDesc('id')
                ->first();
        }

        if (! $next) {
            $next = (clone $base)
                ->where('id', '!=', $this->id)
                ->orderBy('id')
                ->first();
        }

        return ['prev' => $prev, 'next' => $next];
    }
}
