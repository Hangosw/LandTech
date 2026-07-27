<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized', 'redirect' => route('login')], 401);
        }

        $request->validate([
            'property_id' => 'required|integer|exists:properties,id'
        ]);

        $propertyId = $request->property_id;
        $userId = $user['id'] ?? $user->id; // depending on how user is stored in session
        
        $wishlist = Wishlist::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->first();
            
        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['success' => true, 'status' => 'removed']);
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'property_id' => $propertyId
            ]);
            return response()->json(['success' => true, 'status' => 'added']);
        }
    }
}
