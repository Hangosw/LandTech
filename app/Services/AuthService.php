<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserOAuthProvider;
use Exception;

class AuthService
{
    /**
     * Find or create user when logging in via OAuth.
     */
    public function findOrCreateUserFromOAuth(string $provider, array $providerData)
    {
        // 1. Check if OAuth provider connection already exists
        $oauth = UserOAuthProvider::where('provider', $provider)
            ->where('provider_id', $providerData['id'])
            ->first();
        
        if ($oauth) {
            return $oauth->user; // User already linked and found
        }

        // 2. Check if email exists in database
        $user = User::where('email', $providerData['email'])->first();
        
        if ($user) {
            // User exists → offer linking
            return ['action' => 'link', 'user' => $user];
        }

        // 3. Create a brand new user
        $userType = $providerData['user_type'] ?? 'renter';
        if (!in_array($userType, ['renter', 'agent'])) {
            $userType = 'renter';
        }

        $user = User::create([
            'name' => $providerData['name'] ?? 'OAuth User',
            'email' => $providerData['email'],
            'phone' => '09' . mt_rand(10000000, 99999999), // Mock a unique phone
            'user_type' => $userType,
            'is_verified' => false,
            'status' => 'pending',
        ]);

        $this->attachOAuthProvider($user, $provider, $providerData);
        return $user;
    }

    /**
     * Link an OAuth provider to an existing user account.
     */
    public function linkOAuthProvider(User $user, string $provider, array $providerData)
    {
        // Check if user already linked this provider
        if ($user->oauthProviders()->where('provider', $provider)->exists()) {
            throw new Exception('Tài khoản này đã được liên kết với ' . ucfirst($provider));
        }

        // Check if provider_id is already linked to another user
        $existingLink = UserOAuthProvider::where('provider', $provider)
            ->where('provider_id', $providerData['id'])
            ->first();

        if ($existingLink) {
            throw new Exception('Tài khoản ' . ucfirst($provider) . ' này đã được liên kết với một tài khoản khác.');
        }

        return $this->attachOAuthProvider($user, $provider, $providerData);
    }

    /**
     * Attach OAuth provider details to the user.
     */
    private function attachOAuthProvider(User $user, string $provider, array $data)
    {
        return $user->oauthProviders()->create([
            'provider' => $provider,
            'provider_id' => $data['id'],
            'provider_email' => $data['email'],
            'access_token' => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
        ]);
    }
}
