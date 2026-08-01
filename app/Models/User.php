<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasRoles;
    protected $fillable = [
        'phone',
        'email',
        'name',
        'address',
        'password_hash',
        'user_type',
        'nationality',
        'avatar_url',
        'is_verified',
        'status',
        'agent_tier',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    /**
     * Boot function to auto-sync Spatie roles when user_type changes or user is created.
     */
    protected static function booted(): void
    {
        static::created(function ($user) {
            if (!empty($user->user_type) && in_array($user->user_type, ['admin', 'agent', 'renter'])) {
                $user->syncRoles([$user->user_type]);
            }
        });

        static::updated(function ($user) {
            if ($user->wasChanged('user_type') && !empty($user->user_type) && in_array($user->user_type, ['admin', 'agent', 'renter'])) {
                $user->syncRoles([$user->user_type]);
            }
        });
    }

    /**
     * Get the properties posted by this user.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Get transactions where this user is the buyer.
     */
    public function transactionsAsBuyer(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    /**
     * Get transactions where this user is the seller.
     */
    public function transactionsAsSeller(): HasMany
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    /**
     * Get rentals where this user is the renter.
     */
    public function rentalsAsRenter(): HasMany
    {
        return $this->hasMany(Rental::class, 'renter_id');
    }

    /**
     * Get rentals where this user is the owner/landlord.
     */
    public function rentalsAsOwner(): HasMany
    {
        return $this->hasMany(Rental::class, 'owner_id');
    }

    /**
     * Get the OAuth connections for this user.
     */
    public function oauthProviders(): HasMany
    {
        return $this->hasMany(UserOAuthProvider::class);
    }

    /**
     * Check if user is linked to a specific OAuth provider.
     */
    public function isLinkedTo(string $provider): bool
    {
        return $this->oauthProviders()->where('provider', $provider)->exists();
    }

    /**
     * Get list of linked provider names.
     */
    public function getLinkedProviders(): array
    {
        return $this->oauthProviders()->pluck('provider')->toArray();
    }
}