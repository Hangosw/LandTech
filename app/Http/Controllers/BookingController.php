<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
        ]);

        $booking = Booking::updateOrCreate(
            [
                'property_id' => $validated['property_id'],
                'renter_phone' => $validated['contact_phone'],
            ],
            [
                'renter_name' => $validated['contact_name'],
                'scheduled_date' => $validated['booking_date'],
                'scheduled_time' => $validated['booking_time'],
                'status' => 'pending',
            ]
        );

        session()->put('last_booking_' . $validated['property_id'], $booking->id);

        $dateFormatted = Carbon::parse($booking->scheduled_date)->format('d/m/Y');
        
        return response()->json([
            'success' => true,
            'message' => "Sale phụ trách sẽ xác nhận lịch xem nhà {$dateFormatted} lúc {$booking->scheduled_time}."
        ]);
    }

    public function myBookings()
    {
        if (!session()->has('user')) {
            return redirect()->route('login');
        }

        $sessionUser = session('user');
        $userId = is_array($sessionUser) ? ($sessionUser['id'] ?? null) : ($sessionUser->id ?? null);
        
        $bookings = Booking::with('property')
            ->whereHas('property', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('scheduled_time', 'desc')
            ->paginate(10);
            
        return view('pages.agent-bookings', compact('bookings'));
    }
}
